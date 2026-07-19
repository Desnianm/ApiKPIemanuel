<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // perluas enum formulatype dengan 3 nilai baru
        DB::statement("
            ALTER TABLE kpi_jenis 
            MODIFY COLUMN formula_type 
            ENUM('sum','average','count','last_value','minimize','range','binary') 
            NOT NULL
        ");

        // tambah kolom pendukung untuk formula range, binary
        Schema::table('kpi_jenis', function (Blueprint $table) {
            // untuk formula range batas bawah dan atas skala
            $table->decimal('nilai_min', 15, 2)->nullable()->after('field_definitions');
            $table->decimal('nilai_max', 15, 2)->nullable()->after('nilai_min');
            // untuk formula binary jumlah milestone yang harus dicapai
            $table->integer('total_milestone')->nullable()->after('nilai_max');
            // apakah persentase dibatasi maksimal 100%
            $table->boolean('is_capped')->default(true)->after('total_milestone');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_jenis', function (Blueprint $table) {
            $table->dropColumn(['nilai_min', 'nilai_max', 'total_milestone', 'is_capped']);
        });

        DB::statement("
            ALTER TABLE kpi_jenis 
            MODIFY COLUMN formula_type 
            ENUM('sum','average','count','last_value') 
            NOT NULL
        ");
    }
};