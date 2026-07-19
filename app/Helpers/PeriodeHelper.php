<?php

namespace App\Helpers;

use Carbon\Carbon;

class PeriodeHelper
{
    
    public static function hitungPeriode(?string $tanggal = null): array
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();

        // Siklus berakhir di tanggal 25. Jika sudah tanggal 26 ke atas, masuk periode bulan depan.
        if ($date->day > 25) {
            $nextMonth = $date->copy()->addMonth();
            return [
                'bulan' => $nextMonth->month,
                'tahun' => $nextMonth->year,
            ];
        }

        // Jika tanggal 1 sampai 25, tetap masuk siklus bulan berjalan
        return [
            'bulan' => $date->month,
            'tahun' => $date->year,
        ];
    }

   
    public static function getRangePeriode(int $bulan, int $tahun): array
    {
        // Periode mulai dari tanggal 26 bulan sebelumnya
        $start = Carbon::create($tahun, $bulan, 1)
            ->subMonth()
            ->setDay(26);

        // Periode berakhir tanggal 25 bulan berjalan
        $end = Carbon::create($tahun, $bulan, 25);

        return [
            'start' => $start->format('Y-m-d'),
            'end'   => $end->format('Y-m-d'),
        ];
    }
}