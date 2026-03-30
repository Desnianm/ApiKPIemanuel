<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitBisnis;
use App\Models\KategoriUnitBisnis;
use Illuminate\Http\Request;

class UnitBisnisController extends Controller
{
    // GET semua unit bisnis (owner lihat semua, karyawan lihat milik sendiri)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'owner') {
            $unitBisnis = UnitBisnis::with('kategori')->get();
        } else {
            $unitBisnis = UnitBisnis::with('kategori')
                ->where('id', $user->unit_bisnis_id)
                ->get();
        }

        return response()->json([
            'data' => $unitBisnis
        ], 200);
    }

    // GET detail 1 unit bisnis
    public function show($id)
    {
        $unitBisnis = UnitBisnis::with('kategori')->find($id);

        if (!$unitBisnis) {
            return response()->json([
                'message' => 'Unit bisnis tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $unitBisnis
        ], 200);
    }

    // POST buat unit bisnis baru (owner only)
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_unit_bisnis,id',
            'nama'        => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'logo'        => 'nullable|string',
        ]);

        $unitBisnis = UnitBisnis::create($request->all());

        return response()->json([
            'message' => 'Unit bisnis berhasil dibuat.',
            'data'    => $unitBisnis
        ], 201);
    }

    // PUT update unit bisnis (owner only)
    public function update(Request $request, $id)
    {
        $unitBisnis = UnitBisnis::find($id);

        if (!$unitBisnis) {
            return response()->json([
                'message' => 'Unit bisnis tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'kategori_id' => 'sometimes|exists:kategori_unit_bisnis,id',
            'nama'        => 'sometimes|string|max:100',
            'deskripsi'   => 'nullable|string',
            'logo'        => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $unitBisnis->update($request->all());

        return response()->json([
            'message' => 'Unit bisnis berhasil diupdate.',
            'data'    => $unitBisnis
        ], 200);
    }

    // DELETE unit bisnis (owner only)
    public function destroy($id)
    {
        $unitBisnis = UnitBisnis::find($id);

        if (!$unitBisnis) {
            return response()->json([
                'message' => 'Unit bisnis tidak ditemukan.'
            ], 404);
        }

        $unitBisnis->delete();

        return response()->json([
            'message' => 'Unit bisnis berhasil dihapus.'
        ], 200);
    }

    // GET semua kategori unit bisnis
    public function kategori()
    {
        $kategori = KategoriUnitBisnis::with('unitBisnis')->get();

        return response()->json([
            'data' => $kategori
        ], 200);
    }
}