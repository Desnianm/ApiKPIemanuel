<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_templates', function (Blueprint $table) {
            // FK ke katalog — nullable karena data lama belum punya kpi_jenis_id
            $table->foreignId('kpi_jenis_id')
                  ->nullable()
                  ->after('unit_bisnis_id')
                  ->constrained('kpi_jenis')
                  ->onDelete('restrict'); 
            // restrict: jangan boleh hapus kpi_jenis kalau masih ada kpi_template yang pakai

            $table->softDeletes(); // kolom deleted_at untuk soft delete
        });

        // MySQL tidak support alter enum langsung via Blueprint, pakai raw SQL
        DB::statement("
            ALTER TABLE kpi_templates 
            MODIFY COLUMN kategori 
            ENUM('sales','marketing','operasional','keuangan','sdm','lainnya') 
            NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('kpi_templates', function (Blueprint $table) {
            $table->dropForeign(['kpi_jenis_id']);
            $table->dropColumn('kpi_jenis_id');
            $table->dropSoftDeletes();
        });

        DB::statement("
            ALTER TABLE kpi_templates 
            MODIFY COLUMN kategori 
            ENUM('sales','marketing','operasional') 
            NOT NULL
        ");
    }
};