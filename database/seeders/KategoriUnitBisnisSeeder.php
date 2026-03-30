<?php

namespace Database\Seeders;

use App\Models\KategoriUnitBisnis;
use Illuminate\Database\Seeder;

class KategoriUnitBisnisSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Properti', 'deskripsi' => 'Unit bisnis properti jual beli', 'urutan' => 1],
            ['nama' => 'Properti Management', 'deskripsi' => 'Unit bisnis pengelolaan properti (Little Heaven Management)', 'urutan' => 2],
            ['nama' => 'UMKM', 'deskripsi' => 'Unit bisnis UMKM Emanuel Corp', 'urutan' => 3],
        ];

        foreach ($kategoris as $kategori) {
            KategoriUnitBisnis::create($kategori);
        }
    }
}