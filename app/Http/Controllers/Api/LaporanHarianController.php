<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;

class LaporanHarianController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'owner') {
            $laporan = LaporanHarian::with(['unitBisnis', 'user'])
                ->orderBy('tanggal', 'desc')
                ->get();
        } else {
            $laporan = LaporanHarian::with(['unitBisnis', 'user'])
                ->where('unit_bisnis_id', $user->unit_bisnis_id)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return response()->json([
            'data' => $laporan
        ], 200);
    }

    public function show($id)
    {
        $laporan = LaporanHarian::with(['unitBisnis', 'user'])->find($id);

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $laporan
        ], 200);
    }

    public function byPeriode(Request $request, $unitBisnisId)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $laporan = LaporanHarian::with(['unitBisnis', 'user'])
            ->where('unit_bisnis_id', $unitBisnisId)
            ->whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'data'          => $laporan,
            'total_leads'   => $laporan->sum('leads_masuk'),
            'total_closing' => $laporan->sum('closing_deal'),
            'total_laporan' => $laporan->count(),
        ], 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id' => 'required|exists:unit_bisnis,id',
            'tanggal'        => 'required|date',
            'aktivitas'      => 'required|string',
            'leads_masuk'    => 'nullable|integer|min:0',
            'closing_deal'   => 'nullable|integer|min:0',
            'catatan'        => 'nullable|string',
        ]);

        // cek apakah sudah ada laporan di tanggal yang sama
        $existing = LaporanHarian::where('unit_bisnis_id', $request->unit_bisnis_id)
            ->where('user_id', $request->user()->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Laporan untuk tanggal ini sudah ada. Gunakan update jika ingin mengubah.',
                'data'    => $existing
            ], 422);
        }

        $laporan = LaporanHarian::create([
            'unit_bisnis_id' => $request->unit_bisnis_id,
            'user_id'        => $request->user()->id,
            'tanggal'        => $request->tanggal,
            'aktivitas'      => $request->aktivitas,
            'leads_masuk'    => $request->leads_masuk ?? 0,
            'closing_deal'   => $request->closing_deal ?? 0,
            'catatan'        => $request->catatan,
        ]);

        return response()->json([
            'message' => 'Laporan harian berhasil diinput.',
            'data'    => $laporan
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $laporan = LaporanHarian::find($id);

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'aktivitas'    => 'sometimes|string',
            'leads_masuk'  => 'nullable|integer|min:0',
            'closing_deal' => 'nullable|integer|min:0',
            'catatan'      => 'nullable|string',
        ]);

        $laporan->update($request->only([
            'aktivitas', 'leads_masuk', 'closing_deal', 'catatan'
        ]));

        return response()->json([
            'message' => 'Laporan harian berhasil diupdate.',
            'data'    => $laporan
        ], 200);
    }


    public function destroy($id)
    {
        $laporan = LaporanHarian::find($id);

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        $laporan->delete();

        return response()->json([
            'message' => 'Laporan harian berhasil dihapus.'
        ], 200);
    }
}