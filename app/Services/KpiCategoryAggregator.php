<?php

namespace App\Services;

use App\Models\KategoriUnitBisnis;
use App\Models\KpiPeriod;
use App\Models\UnitBisnis;

// menghitung skor unit dan rata rata kategori
// skor unit dihitung rata rata biasa dari semua kpi aktif milik unit itu, tanpa bobot per kpi
class KpiCategoryAggregator
{
    public function __construct(
        private KpiCalculationService $calculationService
    ) {}

    // hitung skor satu unit bisnis untuk periode bulan dan tahun tertentu, balikin nol kalau unit tidak punya kpi period supaya tidak division by zero
    public function hitungSkorUnit(UnitBisnis $unit, int $bulan, int $tahun): float
    {
        $kpiPeriods = KpiPeriod::with('kpiTemplate.kpiJenis')
            ->where('unit_bisnis_id', $unit->id)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->get();

        if ($kpiPeriods->isEmpty()) {
            return 0;
        }

        $persentaseList = $kpiPeriods->map(
            fn (KpiPeriod $kp) => $this->calculationService->hitungPersentaseDenganCap($kp)
        );

        return round($persentaseList->avg(), 2);
    }

    // hitung rata rata skor semua unit dalam satu kategori, plus breakdown skor per unit buat drill down
    public function hitungRataRataKategori(KategoriUnitBisnis $kategori, int $bulan, int $tahun): array
    {
        $unitList = $kategori->unitBisnis;

        $unitScores = $unitList->map(function (UnitBisnis $unit) use ($bulan, $tahun) {
            return [
                'unit_id'   => $unit->id,
                'nama_unit' => $unit->nama,
                'skor'      => $this->hitungSkorUnit($unit, $bulan, $tahun),
            ];
        });

        $rataRata = $unitScores->isNotEmpty()
            ? round($unitScores->avg('skor'), 2)
            : 0;

        return [
            'kategori_id'   => $kategori->id,
            'nama_kategori' => $kategori->nama,
            'rata_rata'     => $rataRata,
            'unit'          => $unitScores->values()->all(),
        ];
    }
}