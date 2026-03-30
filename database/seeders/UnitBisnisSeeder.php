<?php

namespace Database\Seeders;

use App\Models\UnitBisnis;
use Illuminate\Database\Seeder;

class UnitBisnisSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // Properti (kategori_id: 1)
            ['kategori_id' => 1, 'nama' => 'Cluster de Matraman'],
            ['kategori_id' => 1, 'nama' => 'Casa de Slipi'],

            // Properti Management (kategori_id: 2)
            ['kategori_id' => 2, 'nama' => 'Little Bali Villa'],
            ['kategori_id' => 2, 'nama' => 'Seven Terrace Villa'],

            // UMKM (kategori_id: 3)
            ['kategori_id' => 3, 'nama' => "K'way Hair Studio"],
            ['kategori_id' => 3, 'nama' => 'Kios Pangkalan Gas Prigi'],
            ['kategori_id' => 3, 'nama' => 'Kios Pangkalan Gas Rajeg'],
            ['kategori_id' => 3, 'nama' => 'Kios Pangkalan Mbah Bit'],
            ['kategori_id' => 3, 'nama' => 'Part Rent Car'],
        ];

        foreach ($units as $unit) {
            UnitBisnis::create($unit);
        }
    }
}