<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\PeriodeHelper;

class PeriodeController extends Controller
{
    /**
     * GET /api/periode/aktif
     * Return periode yang sedang aktif sesuai logika cut-off tanggal 25
     * Supaya frontend (mobile & web) tidak perlu duplikasi logika ini sendiri
     */
    public function aktif()
    {
        $periode = PeriodeHelper::hitungPeriode();

        // Buat label bulan dalam bahasa Indonesia
        $namaBulan = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'bulan'       => $periode['bulan'],
                'tahun'       => $periode['tahun'],
                'label'       => $namaBulan[$periode['bulan']] . ' ' . $periode['tahun'],
                'range'       => PeriodeHelper::getRangePeriode($periode['bulan'], $periode['tahun']),
                'cutoff_info' => 'Periode bergeser setiap tanggal 26',
            ],
        ]);
    }
}