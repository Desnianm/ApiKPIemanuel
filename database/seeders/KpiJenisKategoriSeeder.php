<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiJenis;
use App\Models\KategoriUnitBisnis;

class KpiJenisKategoriSeeder extends Seeder
{
    public function run(): void
    {
        // ambil id kategori berdasarkan nama bukan hardcode angka supaya aman walau urutan id berubah di environment lain
        $properti = KategoriUnitBisnis::where('nama', 'Properti')->firstOrFail();
        $pm       = KategoriUnitBisnis::where('nama', 'Properti Management')->firstOrFail();
        $umkm     = KategoriUnitBisnis::where('nama', 'UMKM')->firstOrFail();

        // mapping kode kpi ke array kategori yang relevan sesuai keputusan final dari mikel dua belas agustus dua ribu dua puluh enam
        $mapping = [
            'revenue'                => [$properti->id, $pm->id, $umkm->id],
            'occupancy'               => [$pm->id],
            'customer_count'          => [$umkm->id],
            'nights_sold'             => [$pm->id],
            'gas_volume'              => [$umkm->id],
            'service_count'           => [$umkm->id],
            'complaint_count'         => [$properti->id, $pm->id, $umkm->id],
            'maintenance_cost'        => [$properti->id, $pm->id, $umkm->id],
            'stockout_days'           => [$umkm->id],
            'guest_satisfaction'      => [$pm->id],
            'customer_satisfaction'   => [$umkm->id],
            'construction_progress'   => [$properti->id],
            // khusus part rent car tapi level kategori umkm
            'service_schedule'        => [$umkm->id],
        ];

        $jumlahDiproses = 0;

        foreach ($mapping as $kode => $kategoriIds) {
            $kpiJenis = KpiJenis::where('kode', $kode)->first();

            if (!$kpiJenis) {
                $this->command->warn("KPI dengan kode '{$kode}' tidak ditemukan, dilewati.");
                continue;
            }

            $kpiJenis->kategoriUnitBisnis()->sync($kategoriIds);
            $jumlahDiproses++;
        }

        $this->command->info("KpiJenisKategori seeder selesai: {$jumlahDiproses} KPI berhasil di-mapping ke kategori.");
    }
}