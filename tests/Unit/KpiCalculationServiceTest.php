<?php

namespace Tests\Unit;

use App\Models\KpiJenis;
use App\Models\KpiPeriod;
use App\Models\KpiTemplate;
use App\Services\KpiCalculationService;
use PHPUnit\Framework\TestCase;

class KpiCalculationServiceTest extends TestCase
{
    private KpiCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KpiCalculationService();
    }

    private function buatKpiPeriod(array $periodAttrs, array $jenisAttrs, float $capMax = 120): KpiPeriod
    {
        $jenis = new KpiJenis($jenisAttrs);
        $template = new KpiTemplate(['cap_max_persen' => $capMax]);
        $template->setRelation('kpiJenis', $jenis);

        $period = new KpiPeriod($periodAttrs);
        $period->setRelation('kpiTemplate', $template);

        return $period;
    }

    public function test_formula_sum_menghitung_persentase_standar(): void
    {
        $kp = $this->buatKpiPeriod(
            ['target' => 1000, 'realisasi' => 750],
            ['formula_type' => 'sum']
        );

        $this->assertEquals(75.0, $this->service->hitungPersentase($kp));
    }

    public function test_formula_sum_dengan_target_nol_tidak_division_by_zero(): void
    {
        $kp = $this->buatKpiPeriod(
            ['target' => 0, 'realisasi' => 750],
            ['formula_type' => 'sum']
        );

        $this->assertEquals(0, $this->service->hitungPersentase($kp));
    }

    public function test_formula_minimize_menghitung_kebalikan(): void
    {
        $kp = $this->buatKpiPeriod(
            ['target' => 5, 'realisasi' => 3],
            ['formula_type' => 'minimize']
        );

        $this->assertEquals(166.67, $this->service->hitungPersentase($kp));
    }

    public function test_formula_minimize_dengan_realisasi_nol_dianggap_sempurna(): void
    {
        $kp = $this->buatKpiPeriod(
            ['target' => 5, 'realisasi' => 0],
            ['formula_type' => 'minimize']
        );

        $this->assertGreaterThan(100, $this->service->hitungPersentase($kp));
    }

    public function test_formula_range_menghitung_skala_min_max(): void
    {
        $kp = $this->buatKpiPeriod(
            ['realisasi' => 4.2],
            ['formula_type' => 'range', 'nilai_min' => 1, 'nilai_max' => 5]
        );

        $this->assertEquals(80.0, $this->service->hitungPersentase($kp));
    }

    public function test_formula_range_dengan_max_sama_dengan_min_tidak_error(): void
    {
        $kp = $this->buatKpiPeriod(
            ['realisasi' => 4.2],
            ['formula_type' => 'range', 'nilai_min' => 5, 'nilai_max' => 5]
        );

        $this->assertEquals(0, $this->service->hitungPersentase($kp));
    }

    public function test_formula_binary_menghitung_milestone(): void
    {
        $kp = $this->buatKpiPeriod(
            ['realisasi' => 2],
            ['formula_type' => 'binary', 'total_milestone' => 4, 'is_capped' => true]
        );

        $this->assertEquals(50.0, $this->service->hitungPersentase($kp));
    }

    public function test_formula_binary_dicap_ke_100_saat_is_capped_true(): void
    {
        $kp = $this->buatKpiPeriod(
            ['realisasi' => 6],
            ['formula_type' => 'binary', 'total_milestone' => 4, 'is_capped' => true]
        );

        $this->assertEquals(100.0, $this->service->hitungPersentase($kp));
    }

    public function test_cap_max_persen_diterapkan_dengan_benar(): void
    {
        $kp = $this->buatKpiPeriod(
            ['target' => 100, 'realisasi' => 300],
            ['formula_type' => 'sum'],
            capMax: 120
        );

        $this->assertEquals(300.0, $this->service->hitungPersentase($kp));
        $this->assertEquals(120.0, $this->service->hitungPersentaseDenganCap($kp));
    }

    public function test_persentase_di_bawah_cap_tidak_ikut_terpengaruh(): void
    {
        $kp = $this->buatKpiPeriod(
            ['target' => 1000, 'realisasi' => 750],
            ['formula_type' => 'sum'],
            capMax: 120
        );

        $this->assertEquals(75.0, $this->service->hitungPersentaseDenganCap($kp));
    }
}