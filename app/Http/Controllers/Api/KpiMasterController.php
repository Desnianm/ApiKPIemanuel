<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiPeriod;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;

class KpiMasterController extends Controller
{
    /**
     * GET /api/kpi-master/summary
     * Agregat seluruh KPI semua unit bisnis dalam 1 periode
     * Untuk dashboard utama web admin/owner
     * 
     * Query params:
     * - bulan : bulan yang ingin dilihat (default: periode aktif)
     * - tahun : tahun yang ingin dilihat (default: periode aktif)
     */
    public function summary(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000',
        ]);

        $currentPeriode = PeriodeHelper::hitungPeriode();
        $bulan          = $request->bulan ?? $currentPeriode['bulan'];
        $tahun          = $request->tahun ?? $currentPeriode['tahun'];

        $periods = KpiPeriod::with(['unitBisnis', 'kpiTemplate.kpiJenis'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->get();

        // Breakdown per unit bisnis
        $breakdown = $periods->groupBy('unit_bisnis_id')->map(function ($items) {
            $firstItem      = $items->first();
            $totalTarget    = $items->sum('target');
            $totalRealisasi = $items->sum('realisasi');
            $persentase     = $totalTarget > 0
                ? round(($totalRealisasi / $totalTarget) * 100, 2)
                : 0;

            // Tentukan status unit bisnis berdasarkan rata-rata persentase
            $status = $this->tentukanStatus($persentase, $firstItem);

            return [
                'unit_bisnis'     => $firstItem->unitBisnis,
                'total_target'    => $totalTarget,
                'total_realisasi' => $totalRealisasi,
                'persentase'      => $persentase,
                'status'          => $status,
                'jumlah_kpi'      => $items->count(),
                'detail_kpi'      => $items->map(function ($item) {
                    $persen = $item->target > 0
                        ? round(($item->realisasi / $item->target) * 100, 2)
                        : 0;
                    return [
                        'kpi_template_id' => $item->kpi_template_id,
                        'nama_kpi'        => $item->kpiTemplate?->nama,
                        'formula_type'    => $item->kpiTemplate?->kpiJenis?->formula_type,
                        'target'          => $item->target,
                        'realisasi'       => $item->realisasi,
                        'persentase'      => $persen,
                        'status'          => $item->status,
                    ];
                })->values(),
            ];
        })->values();

        // Hitung agregat perusahaan keseluruhan
        $totalTargetPerusahaan    = $periods->sum('target');
        $totalRealisasiPerusahaan = $periods->sum('realisasi');
        $persentasePerusahaan     = $totalTargetPerusahaan > 0
            ? round(($totalRealisasiPerusahaan / $totalTargetPerusahaan) * 100, 2)
            : 0;

        // Hitung jumlah unit bisnis per status
        $jumlahPerStatus = [
            'hijau'  => $breakdown->where('status', 'hijau')->count(),
            'kuning' => $breakdown->where('status', 'kuning')->count(),
            'merah'  => $breakdown->where('status', 'merah')->count(),
        ];

        return response()->json([
            'success' => true,
            'meta'    => [
                'periode_bulan' => (int) $bulan,
                'periode_tahun' => (int) $tahun,
            ],
            'data' => [
                'perusahaan' => [
                    'total_target'    => $totalTargetPerusahaan,
                    'total_realisasi' => $totalRealisasiPerusahaan,
                    'persentase'      => $persentasePerusahaan,
                    'jumlah_per_status' => $jumlahPerStatus,
                ],
                'breakdown_per_unit' => $breakdown->sortByDesc('persentase')->values(),
            ],
        ]);
    }

    /**
     * GET /api/kpi-master/trend
     * Data multi-bulan untuk grafik tren KPI perusahaan
     * Untuk line chart di web admin/owner
     * 
     * Query params:
     * - dari_bulan  : bulan awal (required)
     * - dari_tahun  : tahun awal (required)
     * - sampai_bulan: bulan akhir (required)
     * - sampai_tahun: tahun akhir (required)
     */
    public function trend(Request $request)
    {
        $request->validate([
            'dari_bulan'   => 'required|integer|min:1|max:12',
            'dari_tahun'   => 'required|integer|min:2000',
            'sampai_bulan' => 'required|integer|min:1|max:12',
            'sampai_tahun' => 'required|integer|min:2000',
        ]);

        // Buat list semua periode dalam rentang yang diminta
        $periodeList = $this->generatePeriodeList(
            $request->dari_bulan,
            $request->dari_tahun,
            $request->sampai_bulan,
            $request->sampai_tahun
        );

        // Ambil semua KPI period dalam rentang tersebut
        $allPeriods = KpiPeriod::with(['unitBisnis', 'kpiTemplate'])
            ->where(function ($query) use ($periodeList) {
                foreach ($periodeList as $periode) {
                    $query->orWhere(function ($q) use ($periode) {
                        $q->where('periode_bulan', $periode['bulan'])
                          ->where('periode_tahun', $periode['tahun']);
                    });
                }
            })
            ->get();

        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        // Susun data tren per periode
        $trendData = collect($periodeList)->map(function ($periode) use ($allPeriods, $namaBulan) {
            $periodePeriods = $allPeriods
                ->where('periode_bulan', $periode['bulan'])
                ->where('periode_tahun', $periode['tahun']);

            $totalTarget    = $periodePeriods->sum('target');
            $totalRealisasi = $periodePeriods->sum('realisasi');
            $persentase     = $totalTarget > 0
                ? round(($totalRealisasi / $totalTarget) * 100, 2)
                : 0;

            // Hitung jumlah unit bisnis per status di periode ini
            $unitBisnisIds = $periodePeriods->pluck('unit_bisnis_id')->unique();
            $statusPerUnit = $unitBisnisIds->map(function ($unitId) use ($periodePeriods) {
                $unitPeriods    = $periodePeriods->where('unit_bisnis_id', $unitId);
                $totalT         = $unitPeriods->sum('target');
                $totalR         = $unitPeriods->sum('realisasi');
                $persen         = $totalT > 0 ? ($totalR / $totalT) * 100 : 0;
                $firstItem      = $unitPeriods->first();
                return $this->tentukanStatus($persen, $firstItem);
            });

            return [
                'periode'         => $namaBulan[$periode['bulan']] . ' ' . $periode['tahun'],
                'bulan'           => $periode['bulan'],
                'tahun'           => $periode['tahun'],
                'total_target'    => $totalTarget,
                'total_realisasi' => $totalRealisasi,
                'persentase'      => $persentase,
                'jumlah_per_status' => [
                    'hijau'  => $statusPerUnit->filter(fn($s) => $s === 'hijau')->count(),
                    'kuning' => $statusPerUnit->filter(fn($s) => $s === 'kuning')->count(),
                    'merah'  => $statusPerUnit->filter(fn($s) => $s === 'merah')->count(),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'meta'    => [
                'dari'   => $namaBulan[$request->dari_bulan] . ' ' . $request->dari_tahun,
                'sampai' => $namaBulan[$request->sampai_bulan] . ' ' . $request->sampai_tahun,
                'jumlah_periode' => count($periodeList),
            ],
            'data' => $trendData->values(),
        ]);
    }


    // Helper: Generate list periode dari bulan-tahun awal sampai akhir
    private function generatePeriodeList(
        int $dariBulan,
        int $dariTahun,
        int $sampaiBulan,
        int $sampaiTahun
    ): array {
        $list    = [];
        $bulan   = $dariBulan;
        $tahun   = $dariTahun;

        // Maksimal 24 bulan ke belakang biar tidak overload
        $maxIterasi = 24;
        $iterasi    = 0;

        while (
            ($tahun < $sampaiTahun || ($tahun === $sampaiTahun && $bulan <= $sampaiBulan))
            && $iterasi < $maxIterasi
        ) {
            $list[] = ['bulan' => $bulan, 'tahun' => $tahun];

            $bulan++;
            if ($bulan > 12) {
                $bulan = 1;
                $tahun++;
            }
            $iterasi++;
        }

        return $list;
    }

    //Helper: Tentukan status unit bisnis berdasarkan persentase 
    // Pakai threshold dari KPI period pertama sebagai acuan
    private function tentukanStatus(float $persentase, ?KpiPeriod $period): string
    {
        if (!$period) return 'merah';
        if ($persentase >= $period->threshold_hijau) return 'hijau';
        if ($persentase >= $period->threshold_kuning) return 'kuning';
        return 'merah';
    }
}