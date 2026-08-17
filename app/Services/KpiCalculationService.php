<?php

namespace App\Services;

use App\Models\KpiPeriod;

class KpiCalculationService
{
    // hitung persentase mentah tanpa cap, cabang sesuai formula type
    public function hitungPersentase(KpiPeriod $kpiPeriod): float
    {
        $kpiTemplate = $kpiPeriod->kpiTemplate;
        $kpiJenis    = $kpiTemplate?->kpiJenis;
        $formulaType = $kpiJenis?->formula_type ?? 'sum';

        return match ($formulaType) {
            'minimize' => $this->hitungMinimize($kpiPeriod),
            'range'    => $this->hitungRange($kpiPeriod, $kpiJenis),
            'binary'   => $this->hitungBinary($kpiPeriod, $kpiJenis),
            default    => $this->hitungStandar($kpiPeriod),
        };
    }

    // hitung persentase yang sudah diclamp ke cap max persen, dipakai di company aggregation
    public function hitungPersentaseDenganCap(KpiPeriod $kpiPeriod): float
    {
        $persentase = $this->hitungPersentase($kpiPeriod);
        $capMax     = $kpiPeriod->kpiTemplate?->cap_max_persen ?? 120.00;

        return min($persentase, $capMax);
    }

    // rumus standar realisasi bagi target, buat sum average count last value
    private function hitungStandar(KpiPeriod $kpiPeriod): float
    {
        if ($kpiPeriod->target == 0) {
            return 0;
        }

        return round(($kpiPeriod->realisasi / $kpiPeriod->target) * 100, 2);
    }

    // rumus kebalikan, realisasi kecil artinya bagus
    private function hitungMinimize(KpiPeriod $kpiPeriod): float
    {
        if ($kpiPeriod->realisasi == 0) {
            return 999999;
        }

        if ($kpiPeriod->target == 0) {
            return 0;
        }

        return round(($kpiPeriod->target / $kpiPeriod->realisasi) * 100, 2);
    }

    // rumus skala min max, misal rating satu sampai lima
    private function hitungRange(KpiPeriod $kpiPeriod, $kpiJenis): float
    {
        $min = $kpiJenis->nilai_min ?? 0;
        $max = $kpiJenis->nilai_max ?? 100;

        if ($max == $min) {
            return 0;
        }

        $realisasiClamped = max($min, min($kpiPeriod->realisasi, $max));

        return round((($realisasiClamped - $min) / ($max - $min)) * 100, 2);
    }

    // rumus milestone atau progress bertahap
    private function hitungBinary(KpiPeriod $kpiPeriod, $kpiJenis): float
    {
        $totalMilestone = $kpiJenis->total_milestone ?? 1;

        if ($totalMilestone == 0) {
            $persentase = $kpiPeriod->realisasi >= 1 ? 100 : 0;
        } else {
            $persentase = round(($kpiPeriod->realisasi / $totalMilestone) * 100, 2);
        }

        if ($kpiJenis && $kpiJenis->is_capped) {
            $persentase = min($persentase, 100);
        }

        return $persentase;
    }
}