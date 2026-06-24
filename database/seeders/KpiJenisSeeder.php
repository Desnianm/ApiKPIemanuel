<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiJenis;

class KpiJenisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode'             => 'revenue',
                'nama'             => 'Revenue',
                'kategori'         => 'keuangan',
                'satuan_default'   => 'rupiah',
                'formula_type'     => 'sum', // dijumlahkan setiap ada submission
                'field_definitions' => json_encode([
                    ['label' => 'Nilai Transaksi', 'tipe' => 'number', 'wajib' => true],
                ]),
                'deskripsi' => 'Total pendapatan/revenue dalam satu periode',
                'is_active' => true,
            ],
            [
                'kode'             => 'occupancy',
                'nama'             => 'Tingkat Hunian',
                'kategori'         => 'operasional',
                'satuan_default'   => 'persen',
                'formula_type'     => 'last_value', // snapshot harian, langsung ditimpa
                'field_definitions' => json_encode([
                    ['label' => 'Persentase Hunian', 'tipe' => 'number', 'wajib' => true],
                ]),
                'deskripsi' => 'Persentase tingkat hunian kamar/unit properti (snapshot harian)',
                'is_active' => true,
            ],
            [
                'kode'             => 'customer_count',
                'nama'             => 'Jumlah Pelanggan',
                'kategori'         => 'sales',
                'satuan_default'   => 'unit',
                'formula_type'     => 'count', // hitung jumlah submission, nilai input diabaikan
                'field_definitions' => json_encode([
                    ['label' => 'Nama Pelanggan', 'tipe' => 'text', 'wajib' => true],
                ]),
                'deskripsi' => 'Jumlah pelanggan baru yang dilayani dalam satu periode',
                'is_active' => true,
            ],
            [
                'kode'             => 'nights_sold',
                'nama'             => 'Malam Terjual',
                'kategori'         => 'operasional',
                'satuan_default'   => 'malam',
                'formula_type'     => 'sum', // dijumlahkan setiap ada booking
                'field_definitions' => json_encode([
                    ['label' => 'Jumlah Malam', 'tipe' => 'number', 'wajib' => true],
                ]),
                'deskripsi' => 'Total malam yang terjual (untuk unit villa/properti)',
                'is_active' => true,
            ],
            [
                'kode'             => 'gas_volume',
                'nama'             => 'Volume Penjualan Gas',
                'kategori'         => 'sales',
                'satuan_default'   => 'unit',
                'formula_type'     => 'sum', // dijumlahkan setiap penjualan
                'field_definitions' => json_encode([
                    ['label' => 'Jumlah Tabung', 'tipe' => 'number', 'wajib' => true],
                ]),
                'deskripsi' => 'Total tabung gas yang terjual (untuk kios pangkalan gas)',
                'is_active' => true,
            ],
            [
                'kode'             => 'service_count',
                'nama'             => 'Jumlah Layanan',
                'kategori'         => 'operasional',
                'satuan_default'   => 'unit',
                'formula_type'     => 'count', // hitung jumlah submission
                'field_definitions' => json_encode([
                    ['label' => 'Jenis Layanan', 'tipe' => 'text', 'wajib' => true],
                ]),
                'deskripsi' => 'Jumlah layanan yang diberikan (untuk salon/rent car)',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            KpiJenis::updateOrCreate(
                ['kode' => $item['kode']], // cek berdasarkan kode supaya tidak duplikat
                $item
            );
        }

        $this->command->info('KpiJenis seeder selesai: ' . count($data) . ' jenis KPI berhasil di-seed.');
    }
}