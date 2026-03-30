<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanSop;
use Illuminate\Http\Request;

class LaporanSopController extends Controller
{
    // GET semua laporan SOP
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'owner') {
            $laporan = LaporanSop::with(['unitBisnis', 'user', 'reviewedBy'])
                ->orderBy('tanggal', 'desc')
                ->get();
        } else {
            $laporan = LaporanSop::with(['unitBisnis', 'user', 'reviewedBy'])
                ->where('unit_bisnis_id', $user->unit_bisnis_id)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return response()->json([
            'data' => $laporan
        ], 200);
    }

    // GET detail 1 laporan SOP
    public function show($id)
    {
        $laporan = LaporanSop::with(['unitBisnis', 'user', 'reviewedBy'])->find($id);

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan SOP tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $laporan
        ], 200);
    }

    // POST buat laporan SOP baru (karyawan)
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id' => 'required|exists:unit_bisnis,id',
            'tanggal'        => 'required|date',
            'deskripsi'      => 'required|string',
            'foto'           => 'nullable|string',
            'lokasi'         => 'nullable|string',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        $laporan = LaporanSop::create([
            'unit_bisnis_id' => $request->unit_bisnis_id,
            'user_id'        => $request->user()->id,
            'tanggal'        => $request->tanggal,
            'deskripsi'      => $request->deskripsi,
            'foto'           => $request->foto,
            'lokasi'         => $request->lokasi,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'status'         => 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan SOP berhasil diinput.',
            'data'    => $laporan
        ], 201);
    }

    // PATCH review laporan SOP (owner only)
    public function review(Request $request, $id)
    {
        $laporan = LaporanSop::find($id);

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan SOP tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'status'        => 'required|in:approved,rejected',
            'catatan_owner' => 'nullable|string',
        ]);

        $laporan->update([
            'status'        => $request->status,
            'catatan_owner' => $request->catatan_owner,
            'reviewed_by'   => $request->user()->id,
            'reviewed_at'   => now(),
        ]);

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';

        return response()->json([
            'message' => "Laporan SOP berhasil {$statusText}.",
            'data'    => $laporan
        ], 200);
    }

    // DELETE laporan SOP (owner only)
    public function destroy($id)
    {
        $laporan = LaporanSop::find($id);

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan SOP tidak ditemukan.'
            ], 404);
        }

        $laporan->delete();

        return response()->json([
            'message' => 'Laporan SOP berhasil dihapus.'
        ], 200);
    }
}