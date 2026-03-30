<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'unit_bisnis', 'manajer', 'karyawan') NOT NULL DEFAULT 'karyawan'");
        DB::statement("UPDATE users SET role = 'karyawan' WHERE role = 'unit_bisnis'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'manajer', 'karyawan') NOT NULL DEFAULT 'karyawan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'unit_bisnis', 'manajer', 'karyawan') NOT NULL DEFAULT 'unit_bisnis'");
        DB::statement("UPDATE users SET role = 'unit_bisnis' WHERE role = 'karyawan'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'unit_bisnis') NOT NULL DEFAULT 'unit_bisnis'");
    }
};