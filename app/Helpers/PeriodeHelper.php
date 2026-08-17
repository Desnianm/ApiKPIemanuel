<?php

namespace App\Helpers;

use Carbon\Carbon;

class PeriodeHelper
{
    
    public static function hitungPeriode(?string $tanggal = null): array
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();

        // siklus selesai di tgl 25 dan 26 masuk bulan depan 
        if ($date->day > 25) {
            $nextMonth = $date->copy()->addMonth();
            return [
                'bulan' => $nextMonth->month,
                'tahun' => $nextMonth->year,
            ];
        }

        // tanggal 1 smpai 25, masuk siklus bulan berjalan
        return [
            'bulan' => $date->month,
            'tahun' => $date->year,
        ];
    }

   
    public static function getRangePeriode(int $bulan, int $tahun): array
    {
        // periode mulai tgl 26 bln sblumnya
        $start = Carbon::create($tahun, $bulan, 1)
            ->subMonth()
            ->setDay(26);

        // periode berakhir tgl 25 bulan berjalan 
        $end = Carbon::create($tahun, $bulan, 25);

        return [
            'start' => $start->format('Y-m-d'),
            'end'   => $end->format('Y-m-d'),
        ];
    }
}