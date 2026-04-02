<?php

namespace App\Helpers;

use Carbon\Carbon;

class PeriodeHelper
{
    /**
     * Hitung periode (bulan & tahun) berdasarkan tanggal input
     * Cut off: tanggal 25
     * - Tanggal <= 25 → periode bulan ini
     * - Tanggal > 25 → periode bulan depan
     */
    public static function hitungPeriode(?string $tanggal = null): array
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();

        if ($date->day <= 25) {
            return [
                'bulan' => $date->month,
                'tahun' => $date->year,
            ];
        } else {
            $nextMonth = $date->copy()->addMonth();
            return [
                'bulan' => $nextMonth->month,
                'tahun' => $nextMonth->year,
            ];
        }
    }

    /**
     * Get tanggal mulai & akhir periode
     */
    public static function getRangePeriode(int $bulan, int $tahun): array
    {
        // Periode mulai dari tanggal 26 bulan sebelumnya
        $start = Carbon::create($tahun, $bulan, 1)
            ->subMonth()
            ->setDay(26);

        // Periode berakhir tanggal 25 bulan ini
        $end = Carbon::create($tahun, $bulan, 25);

        return [
            'start' => $start->format('Y-m-d'),
            'end'   => $end->format('Y-m-d'),
        ];
    }
}