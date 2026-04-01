<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sop;
use Illuminate\Http\Request;

class SopController extends Controller
{
    // GET semua SOP
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'owner') {
            $sop = Sop::with('unitBisnis')
                ->where('is_active', true)
                ->orderBy('urutan')
                ->get();
        } else {
            $sop = Sop::with('unitBisnis')
                ->where('is_active', true)
                ->where(function ($query) use ($user) {
                    $query->whereNull('unit_bisnis_id')
                        ->orWhere('unit_bisnis_id', $user->unit_bisnis_id);
                })
                ->orderBy('urutan')
                ->get();
        }

        return response()->json([
            'data' => $sop
        ], 200);
    }

    // GET detail 1 SOP
    public function show($id)
    {
        $sop = Sop::with('unitBisnis')->find($id);

        if (!$sop) {
            return response()->json([
                'message' => 'SOP tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $sop
        ], 200);
    }

    // POST buat SOP baru (owner only)
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id' => 'nullable|exists:unit_bisnis,id',
            'judul'          => 'required|string|max:200',
            'konten'         => 'required|string',
            'urutan'         => 'nullable|integer|min:0',
        ]);

        $sop = Sop::create([
            'unit_bisnis_id' => $request->unit_bisnis_id,
            'judul'          => $request->judul,
            'konten'         => $request->konten,
            'urutan'         => $request->urutan ?? 0,
            'is_active'      => true,
        ]);

        return response()->json([
            'message' => 'SOP berhasil dibuat.',
            'data'    => $sop
        ], 201);
    }

    // PUT update SOP (owner only)
    public function update(Request $request, $id)
    {
        $sop = Sop::find($id);

        if (!$sop) {
            return response()->json([
                'message' => 'SOP tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'unit_bisnis_id' => 'nullable|exists:unit_bisnis,id',
            'judul'          => 'sometimes|string|max:200',
            'konten'         => 'sometimes|string',
            'urutan'         => 'nullable|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $sop->update($request->all());

        return response()->json([
            'message' => 'SOP berhasil diupdate.',
            'data'    => $sop
        ], 200);
    }

    // DELETE SOP (owner only)
    public function destroy($id)
    {
        $sop = Sop::find($id);

        if (!$sop) {
            return response()->json([
                'message' => 'SOP tidak ditemukan.'
            ], 404);
        }

        $sop->delete();

        return response()->json([
            'message' => 'SOP berhasil dihapus.'
        ], 200);
    }
}