<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\PeriodeHelper;
use App\Services\KpiCompanyAggregator;
use Illuminate\Http\Request;

class KpiMasterCompanyController extends Controller
{
    public function __construct(
        private KpiCompanyAggregator $companyAggregator
    ) {}

    // aggregasi skor company berdasarkan kategori, unit, bobot, dan capping KPI.
    public function company(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000',
        ]);

        $currentPeriode = PeriodeHelper::hitungPeriode();
        $bulan          = $request->bulan ?? $currentPeriode['bulan'];
        $tahun          = $request->tahun ?? $currentPeriode['tahun'];

        $hasil = $this->companyAggregator->hitungSkorCompany($bulan, $tahun);

        return response()->json([
            'success' => true,
            'data'    => $hasil,
        ]);
    }
}