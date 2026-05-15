<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
{
    // Owner accounts
    $owners = [
        ['name' => 'Om Agung', 'email' => 'agung@emanuelcorp.id', 'password' => 'AgungCorp2024!', 'role' => 'owner'],
        ['name' => 'Tante Valen', 'email' => 'valen@emanuelcorp.id', 'password' => 'ValenCorp2024!', 'role' => 'owner'],
        ['name' => 'Corporate Secretary', 'email' => 'secretary@emanuelcorp.id', 'password' => 'SecretaryCorp2024!', 'role' => 'owner'],
    ];

            // Owner accounts
        foreach ($owners as $owner) {
            User::updateOrCreate(
                ['email' => $owner['email']],
                [
                    'name' => $owner['name'],
                    'password' => Hash::make($owner['password']),
                    'role' => $owner['role'],
                    'unit_bisnis_id' => null,
                    'is_active' => true,
                    'photo' => 'photos/default.jpeg',
                ]
            );
        }

    // Karyawan accounts
    $unitAccounts = [
        ['name' => 'Cluster de Matraman', 'email' => 'cdm@emanuelcorp.id', 'password' => 'CDMCorp2024!', 'unit_bisnis_id' => 1],
        ['name' => 'Casa de Slipi', 'email' => 'cds@emanuelcorp.id', 'password' => 'CDSCorp2024!', 'unit_bisnis_id' => 2],
        ['name' => 'Little Bali Villa', 'email' => 'lbv@emanuelcorp.id', 'password' => 'LBVCorp2024!', 'unit_bisnis_id' => 3],
        ['name' => 'Seven Terrace Villa', 'email' => 'stv@emanuelcorp.id', 'password' => 'STVCorp2024!', 'unit_bisnis_id' => 4],
        ['name' => "K'way Hair Studio", 'email' => 'kway@emanuelcorp.id', 'password' => 'KWAYCorp2024!', 'unit_bisnis_id' => 5],
        ['name' => 'Kios Gas Prigi', 'email' => 'gasprigi@emanuelcorp.id', 'password' => 'GasPrigi2024!', 'unit_bisnis_id' => 6],
        ['name' => 'Kios Gas Rajeg', 'email' => 'gasrajeg@emanuelcorp.id', 'password' => 'GasRajeg2024!', 'unit_bisnis_id' => 7],
        ['name' => 'Kios Mbah Bit', 'email' => 'mbahbit@emanuelcorp.id', 'password' => 'MbahBit2024!', 'unit_bisnis_id' => 8],
        ['name' => 'Part Rent Car', 'email' => 'rentcar@emanuelcorp.id', 'password' => 'RentCar2024!', 'unit_bisnis_id' => 9],
    ];

    // Karyawan accounts
    foreach ($unitAccounts as $account) {
        User::updateOrCreate(
            ['email' => $account['email']],
            [
                'name' => $account['name'],
                'password' => Hash::make($account['password']),
                'role' => 'karyawan',
                'unit_bisnis_id' => $account['unit_bisnis_id'],
                'is_active' => true,
                'photo' => 'photos/default.jpeg',
            ]
        );
    }
}
}