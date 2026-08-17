<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\PeriodeHelper;

class PeriodeController extends Controller
{
    
    public function aktif()
    {
        $periode = PeriodeHelper::hitungPeriode();

        // buat label bulan dalam bahasa Indonesia
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