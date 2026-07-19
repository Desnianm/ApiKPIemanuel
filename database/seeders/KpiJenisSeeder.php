<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiJenis;

class KpiJenisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // rumus sum (dijumlahkan setiap submission)
            [
                'kode'              => 'revenue',
                'nama'              => 'Revenue',
                'kategori'          => 'keuangan',
                'satuan_default'    => 'rupiah',
                'formula_type'      => 'sum',
                'field_definitions' => json_encode([
                    ['label' => 'Nilai Transaksi', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Total pendapatan/revenue dalam satu periode',
                'is_active'         => true,
            ],
            [
                'kode'              => 'nights_sold',
                'nama'              => 'Malam Terjual',
                'kategori'          => 'operasional',
                'satuan_default'    => 'malam',
                'formula_type'      => 'sum',
                'field_definitions' => json_encode([
                    ['label' => 'Jumlah Malam', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Total malam yang terjual (untuk unit villa/properti)',
                'is_active'         => true,
            ],
            [
                'kode'              => 'gas_volume',
                'nama'              => 'Volume Penjualan Gas',
                'kategori'          => 'sales',
                'satuan_default'    => 'unit',
                'formula_type'      => 'sum',
                'field_definitions' => json_encode([
                    ['label' => 'Jumlah Tabung', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Total tabung gas yang terjual (untuk kios pangkalan gas)',
                'is_active'         => true,
            ],


            // rumus count (hitung jumlah submission)
            [
                'kode'              => 'customer_count',
                'nama'              => 'Jumlah Pelanggan',
                'kategori'          => 'sales',
                'satuan_default'    => 'unit',
                'formula_type'      => 'count',
                'field_definitions' => json_encode([
                    ['label' => 'Nama Pelanggan', 'tipe' => 'text', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Jumlah pelanggan baru yang dilayani dalam satu periode',
                'is_active'         => true,
            ],
            [
                'kode'              => 'service_count',
                'nama'              => 'Jumlah Layanan',
                'kategori'          => 'operasional',
                'satuan_default'    => 'unit',
                'formula_type'      => 'count',
                'field_definitions' => json_encode([
                    ['label' => 'Jenis Layanan', 'tipe' => 'text', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Jumlah layanan yang diberikan (untuk salon/rent car)',
                'is_active'         => true,
            ],

            // rumus last value (timpa nilai terbaru)
            [
                'kode'              => 'occupancy',
                'nama'              => 'Tingkat Hunian',
                'kategori'          => 'operasional',
                'satuan_default'    => 'persen',
                'formula_type'      => 'last_value',
                'field_definitions' => json_encode([
                    ['label' => 'Persentase Hunian', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Persentase tingkat hunian kamar/unit properti (snapshot harian)',
                'is_active'         => true,
            ],

            // rumus minimaze (semakin kecil semakin bagus)
            [
                'kode'              => 'complaint_count',
                'nama'              => 'Jumlah Komplain',
                'kategori'          => 'operasional',
                'satuan_default'    => 'unit',
                'formula_type'      => 'minimize',
                'field_definitions' => json_encode([
                    ['label' => 'Jumlah Komplain', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Jumlah komplain pelanggan — semakin sedikit semakin bagus',
                'is_active'         => true,
            ],
            [
                'kode'              => 'maintenance_cost',
                'nama'              => 'Biaya Maintenance',
                'kategori'          => 'keuangan',
                'satuan_default'    => 'rupiah',
                'formula_type'      => 'minimize',
                'field_definitions' => json_encode([
                    ['label' => 'Biaya Aktual', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Biaya maintenance aktual vs budget — semakin hemat semakin bagus',
                'is_active'         => true,
            ],
            [
                'kode'              => 'stockout_days',
                'nama'              => 'Hari Stok Kosong',
                'kategori'          => 'operasional',
                'satuan_default'    => 'unit',
                'formula_type'      => 'minimize',
                'field_definitions' => json_encode([
                    ['label' => 'Jumlah Hari Stok Kosong', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Jumlah hari stok gas kosong — target 0 hari (untuk kios gas)',
                'is_active'         => true,
            ],


            // rumus range (berbasis skala min-max)
            [
                'kode'              => 'guest_satisfaction',
                'nama'              => 'Rating Kepuasan Tamu',
                'kategori'          => 'sdm',
                'satuan_default'    => 'lainnya',
                'formula_type'      => 'range',
                'field_definitions' => json_encode([
                    ['label' => 'Rating (1-5)', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => 1,
                'nilai_max'         => 5,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Rating kepuasan tamu dari platform booking (skala 1-5)',
                'is_active'         => true,
            ],
            [
                'kode'              => 'customer_satisfaction',
                'nama'              => 'Rating Kepuasan Pelanggan',
                'kategori'          => 'sdm',
                'satuan_default'    => 'lainnya',
                'formula_type'      => 'range',
                'field_definitions' => json_encode([
                    ['label' => 'Rating (1-5)', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => 1,
                'nilai_max'         => 5,
                'total_milestone'   => null,
                'is_capped'         => true,
                'deskripsi'         => 'Rating kepuasan pelanggan (skala 1-5) untuk salon/jasa',
                'is_active'         => true,
            ],

            // rumus binary (milestone/ya-tidak)
            [
                'kode'              => 'construction_progress',
                'nama'              => 'Progres Pembangunan',
                'kategori'          => 'operasional',
                'satuan_default'    => 'lainnya',
                'formula_type'      => 'binary',
                'field_definitions' => json_encode([
                    ['label' => 'Milestone Tercapai', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => 4, // pondasi, struktur, finishing, serah terima
                'is_capped'         => true,
                'deskripsi'         => 'Progres pembangunan properti (4 milestone: pondasi, struktur, finishing, serah terima)',
                'is_active'         => true,
            ],
            [
                'kode'              => 'service_schedule',
                'nama'              => 'Kepatuhan Jadwal Servis',
                'kategori'          => 'operasional',
                'satuan_default'    => 'lainnya',
                'formula_type'      => 'binary',
                'field_definitions' => json_encode([
                    ['label' => 'Servis Terlaksana', 'tipe' => 'number', 'wajib' => true],
                ]),
                'nilai_min'         => null,
                'nilai_max'         => null,
                'total_milestone'   => 12, // target 12x servis per tahun
                'is_capped'         => true,
                'deskripsi'         => 'Kepatuhan jadwal servis berkala kendaraan (untuk rent car)',
                'is_active'         => true,
            ],
        ];

        foreach ($data as $item) {
            KpiJenis::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }

        $this->command->info('KpiJenis seeder selesai: ' . count($data) . ' jenis KPI berhasil di-seed.');
    }
}