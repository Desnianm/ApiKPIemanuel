<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiPeriod;
use App\Models\KpiTemplate;
use App\Helpers\PeriodeHelper; // Pastikan helper di-import
use Illuminate\Http\Request;

class KpiPeriodController extends Controller
{
    // Ambil data KPI periode aktif
    public function index(Request $request)
    {
        $user = $request->user();
        // Memanggil fungsi dari Helper terpusat
        $currentPeriode = PeriodeHelper::hitungPeriode();

        $query = KpiPeriod::with(['unitBisnis', 'kpiTemplate'])
            ->where('periode_bulan', $currentPeriode['bulan'])
            ->where('periode_tahun', $currentPeriode['tahun']);

        if ($user->role !== 'owner') {
            $query->where('unit_bisnis_id', $user->unit_bisnis_id);
        }

        $periods = $query->get();

        return response()->json([
            'current_cycle' => [
                'bulan' => $currentPeriode['bulan'],
                'tahun' => $currentPeriode['tahun'],
                'info'  => 'Periode otomatis bergeser setiap tanggal 26'
            ],
            'data' => $periods
        ], 200);
    }

    // Cari histori KPI berdasarkan bulan dan tahun
    public function byPeriode(Request $request, $unitBisnisId)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $periods = KpiPeriod::with(['unitBisnis', 'kpiTemplate'])
            ->where('unit_bisnis_id', $unitBisnisId)
            ->where('periode_bulan', $request->bulan)
            ->where('periode_tahun', $request->tahun)
            ->get();

        return response()->json([
            'data' => $periods
        ], 200);
    }

    // Ambil rekap KPI semua unit bisnis untuk dashboard & ranking
    public function summary(Request $request)
    {
        $currentPeriode = PeriodeHelper::hitungPeriode();
        $bulan = $request->get('bulan', $currentPeriode['bulan']);
        $tahun = $request->get('tahun', $currentPeriode['tahun']);

        $periods = KpiPeriod::with(['unitBisnis', 'kpiTemplate'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->get();

        // Hitung total target, realisasi, dan persentase dengan proteksi data kosong
        $summary = $periods->groupBy('unit_bisnis_id')->map(function ($items) {
            $firstItem = $items->first();
            
            $totalTarget    = $items->sum('target');
            $totalRealisasi = $items->sum('realisasi');
            $persentase     = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100, 2) : 0;

            return [
                'unit_bisnis'     => $firstItem ? $firstItem->unitBisnis : null,
                'total_target'    => $totalTarget,
                'total_realisasi' => $totalRealisasi,
                'persentase'      => $persentase,
                'status'          => $firstItem ? $this->hitungStatus($persentase, $firstItem) : 'merah',
                'detail'          => $items,
            ];
        })->values();

        // Urutkan dari persentase tertinggi
        $ranked = $summary->sortByDesc('persentase')->values();

        return response()->json([
            'meta' => [
                'periode_bulan' => (int)$bulan,
                'periode_tahun' => (int)$tahun
            ],
            'data' => $ranked
        ], 200);
    }

    // Tambah KPI periode baru
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id'  => 'required|exists:unit_bisnis,id',
            'kpi_template_id' => 'required|exists:kpi_templates,id',
            'periode_bulan'   => 'required|integer|min:1|max:12',
            'periode_tahun'   => 'required|integer|min:2000',
            'target'          => 'required|numeric|min:0',
            'threshold_hijau' => 'required|numeric|min:0|max:100',
            'threshold_kuning'=> 'required|numeric|min:0|max:100',
        ]);

        $existing = KpiPeriod::where('unit_bisnis_id', $request->unit_bisnis_id)
            ->where('kpi_template_id', $request->kpi_template_id)
            ->where('periode_bulan', $request->periode_bulan)
            ->where('periode_tahun', $request->periode_tahun)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'KPI period untuk bulan & tahun ini sudah ada.'
            ], 422);
        }

        $period = KpiPeriod::create([
            'unit_bisnis_id'  => $request->unit_bisnis_id,
            'kpi_template_id' => $request->kpi_template_id,
            'periode_bulan'   => $request->periode_bulan,
            'periode_tahun'   => $request->periode_tahun,
            'target'          => $request->target,
            'realisasi'       => 0,
            'threshold_hijau' => $request->threshold_hijau,
            'threshold_kuning'=> $request->threshold_kuning,
            'status'          => 'merah',
        ]);

        return response()->json([
            'message' => 'KPI period berhasil dibuat.',
            'data'    => $period
        ], 201);
    }

    // Update nominal realisasi KPI
    public function updateRealisasi(Request $request, $id)
    {
        $period = KpiPeriod::find($id);

        if (!$period) {
            return response()->json([
                'message' => 'KPI period tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'realisasi' => 'required|numeric|min:0',
        ]);

        $period->realisasi = $request->realisasi;
        $period->status    = $this->hitungStatusFromPeriod($period);
        $period->save();

        return response()->json([
            'message'    => 'Realisasi KPI berhasil diupdate.',
            'data'       => $period,
            'persentase' => $period->target > 0
                ? round(($period->realisasi / $period->target) * 100, 2)
                : 0,
        ], 200);
    }

    // update target dan batas threshold warna KPI
    public function update(Request $request, $id)
    {
        $period = KpiPeriod::find($id);

        if (!$period) {
            return response()->json([
                'message' => 'KPI period tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'target'          => 'sometimes|numeric|min:0',
            'threshold_hijau' => 'sometimes|numeric|min:0|max:100',
            'threshold_kuning'=> 'sometimes|numeric|min:0|max:100',
        ]);

        $period->update($request->only(['target', 'threshold_hijau', 'threshold_kuning']));

        $period->status = $this->hitungStatusFromPeriod($period);
        $period->save();

        return response()->json([
            'message' => 'KPI period berhasil diupdate.',
            'data'    => $period
        ], 200);
    }

    // delete KPI periode
    public function destroy($id)
    {
        $period = KpiPeriod::find($id);

        if (!$period) {
            return response()->json([
                'message' => 'KPI period tidak ditemukan.'
            ], 404);
        }

        $period->delete();

        return response()->json([
            'message' => 'KPI period berhasil dihapus.'
        ], 200);
    }

    // hitung status warna berdasarkan objek period
    private function hitungStatusFromPeriod(KpiPeriod $period): string
    {
        if ($period->target == 0) return 'merah';

        $persentase = ($period->realisasi / $period->target) * 100;

        if ($persentase >= $period->threshold_hijau) {
            return 'hijau';
        } elseif ($persentase >= $period->threshold_kuning) {
            return 'kuning';
        } else {
            return 'merah';
        }
    }

    // hitung status warna berdasarkan persentase angka
    private function hitungStatus(float $persentase, KpiPeriod $period): string
    {
        if ($persentase >= $period->threshold_hijau) {
            return 'hijau';
        } elseif ($persentase >= $period->threshold_kuning) {
            return 'kuning';
        } else {
            return 'merah';
        }
    }
}