<?php

namespace App\Services;

use App\Models\BobotKategoriPeriode;
use App\Models\KategoriUnitBisnis;

// menghitung skor company dari rata rata tiap kategori dikalikan bobot strategisnya untuk periode tahun tertentu
class KpiCompanyAggregator
{
    public function __construct(
        private KpiCategoryAggregator $categoryAggregator
    ) {}

    public function hitungSkorCompany(int $bulan, int $tahun): array
    {
        $kategoriList = KategoriUnitBisnis::with('unitBisnis')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $breakdown    = [];
        $skorCompany  = 0;

        foreach ($kategoriList as $kategori) {
            $bobot = BobotKategoriPeriode::where('kategori_id', $kategori->id)
                ->where('periode', $tahun)
                ->value('bobot_persen') ?? 0;

            $hasilKategori = $this->categoryAggregator->hitungRataRataKategori($kategori, $bulan, $tahun);

            $kontribusi = round(($bobot / 100) * $hasilKategori['rata_rata'], 2);

            $hasilKategori['bobot_persen'] = (float) $bobot;
            $hasilKategori['kontribusi']   = $kontribusi;

            $skorCompany += $kontribusi;
            $breakdown[]  = $hasilKategori;
        }

        return [
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'skor_company' => round($skorCompany, 2),
            'kategori'     => $breakdown,
        ];
    }
}