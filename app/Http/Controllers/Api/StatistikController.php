<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;

class StatistikController extends Controller
{

    public function aktivitas(Request $request)
    {
        $request->validate([
            'tanggal'        => 'nullable|date',
            'unit_bisnis_id' => 'nullable|exists:unit_bisnis,id',
            'user_id'        => 'nullable|exists:users,id',
            'per_page'       => 'nullable|integer|min:1|max:100',
        ]);

        $query = FormSubmission::with([
            'formTemplate',
            'unitBisnis',
            'user',
            'values.formField',
        ]);

        // filter by tanggal
        if ($request->tanggal) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // filter by unit bisnis
        if ($request->unit_bisnis_id) {
            $query->where('unit_bisnis_id', $request->unit_bisnis_id);
        }

        // filter by user/karyawan
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $perPage = $request->per_page ?? 20;
        $data    = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }


    public function aktivitasTrend(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000',
        ]);

        // default ke periode aktif kalau tidak diisi
        $currentPeriode = PeriodeHelper::hitungPeriode();
        $bulan          = $request->bulan ?? $currentPeriode['bulan'];
        $tahun          = $request->tahun ?? $currentPeriode['tahun'];

        // ambil range tanggal periode ini (cut-off tgl 25)
        $range = PeriodeHelper::getRangePeriode($bulan, $tahun);

        // hitung jumlah submission per hari dalam range periode
        $trend = FormSubmission::selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah')
            ->whereBetween('created_at', [$range['start'] . ' 00:00:00', $range['end'] . ' 23:59:59'])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // breakdown per unit bisnis juga
        $breakdownPerUnit = FormSubmission::selectRaw('unit_bisnis_id, COUNT(*) as jumlah')
            ->whereBetween('created_at', [$range['start'] . ' 00:00:00', $range['end'] . ' 23:59:59'])
            ->with('unitBisnis')
            ->groupBy('unit_bisnis_id')
            ->get()
            ->map(function ($item) {
                return [
                    'unit_bisnis_id'   => $item->unit_bisnis_id,
                    'nama_unit_bisnis' => $item->unitBisnis?->nama,
                    'jumlah'           => $item->jumlah,
                ];
            });

        return response()->json([
            'success' => true,
            'meta'    => [
                'bulan'       => (int) $bulan,
                'tahun'       => (int) $tahun,
                'range_start' => $range['start'],
                'range_end'   => $range['end'],
            ],
            'data' => [
                'trend_harian'     => $trend,
                'breakdown_per_unit' => $breakdownPerUnit,
                'total_submission' => $trend->sum('jumlah'),
            ],
        ]);
    }
}