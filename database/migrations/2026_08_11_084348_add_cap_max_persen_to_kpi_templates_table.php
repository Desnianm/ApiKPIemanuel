<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_templates', function (Blueprint $table) {
            // batas atas persentase pencapaian kpi individual dipakai saat menghitung skor unit untuk company aggregation supaya outlier tidak mendistorsi rata rata
            // ditaruh di kpi templates bukan kpi jenis karena ini konfigurasi per instance kpi bukan definisi katalog
            $table->decimal('cap_max_persen', 6, 2)
                  ->default(120.00)
                  ->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_templates', function (Blueprint $table) {
            $table->dropColumn('cap_max_persen');
        });
    }
};