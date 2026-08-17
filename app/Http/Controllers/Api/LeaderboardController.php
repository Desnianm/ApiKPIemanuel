<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitBisnis;
use App\Models\KpiPeriod;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    //ranking unit bisnis
    public function index(Request $request)
    {
        $periode = PeriodeHelper::hitungPeriode();
        $bulan   = $request->query('bulan', $periode['bulan']);
        $tahun   = $request->query('tahun', $periode['tahun']);

        // load relasi users agar bisa ambil foto profil salah satu usernya
        $unitBisnisList = UnitBisnis::with(['kategori', 'users'])
            ->where('is_active', true)
            ->get();

        $leaderboard = [];

        foreach ($unitBisnisList as $unitBisnis) {
            $kpiPeriods = KpiPeriod::where('unit_bisnis_id', $unitBisnis->id)
                ->where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->get();


            $userProfile = $unitBisnis->users->first();
            $photoPath = ($userProfile && $userProfile->photo) 
                         ? $userProfile->photo 
                         : 'photos/default.jpeg';
            
            $photoUrl = asset('storage/' . $photoPath);

            if ($kpiPeriods->isEmpty()) {
                $leaderboard[] = [
                    'unit_bisnis_id'   => $unitBisnis->id,
                    'nama'             => $unitBisnis->nama,
                    'photo_url'        => $photoUrl, // Tambahkan foto di sini
                    'kategori'         => optional($unitBisnis->kategori)->nama ?? '-',
                    'total_kpi'        => 0,
                    'rata_rata_persen' => 0,
                    'status'           => 'merah',
                    'indikator'        => 'UNDERPERFORM',
                    'detail_kpi'       => [],
                ];
                continue;
            }

            $detailKpi   = [];
            $totalPersen = 0;

            foreach ($kpiPeriods as $kpi) {
                $persen = $kpi->target > 0
                    ? round(($kpi->realisasi / $kpi->target) * 100, 2)
                    : 0;

                $totalPersen += $persen;

                $detailKpi[] = [
                    'kpi_template_id' => $kpi->kpi_template_id,
                    'nama_kpi'        => $kpi->kpiTemplate->nama ?? '-',
                    'target'          => $kpi->target,
                    'realisasi'       => $kpi->realisasi,
                    'persentase'      => $persen,
                    'status'          => $kpi->status,
                ];
            }

            $rataRata = round($totalPersen / count($kpiPeriods), 2);

            $firstKpi = $kpiPeriods->first();
            if ($rataRata >= $firstKpi->threshold_hijau) {
                $statusKeseluruhan = 'hijau';
            } elseif ($rataRata >= $firstKpi->threshold_kuning) {
                $statusKeseluruhan = 'kuning';
            } else {
                $statusKeseluruhan = 'merah';
            }

            $leaderboard[] = [
                'unit_bisnis_id'   => $unitBisnis->id,
                'nama'             => $unitBisnis->nama,
                'photo_url'        => $photoUrl, 
                'kategori'         => optional($unitBisnis->kategori)->nama ?? '-',
                'total_kpi'        => count($kpiPeriods),
                'rata_rata_persen' => $rataRata,
                'status'           => $statusKeseluruhan,
                'indikator'        => $this->getIndikator($statusKeseluruhan),
                'detail_kpi'       => $detailKpi,
            ];
        }

        // urutkan berdasarkan persentase tertinggi
        usort($leaderboard, function ($a, $b) {
            return $b['rata_rata_persen'] <=> $a['rata_rata_persen'];
        });

        foreach ($leaderboard as $index => &$item) {
            $item['ranking'] = $index + 1;
        }

        return response()->json([
            'success' => true,
            'periode' => [
                'bulan' => (int) $bulan,
                'tahun' => (int) $tahun,
            ],
            'data'    => $leaderboard,
        ]);
    }

    //konversi merah, kuning, hijau
    private function getIndikator(string $status): string
    {
        return match($status) {
            'hijau'  => 'OVERPERFORM',
            'kuning' => 'ON TRACK',
            'merah'  => 'UNDERPERFORM',
            default  => 'UNDERPERFORM',
        };
    }
}