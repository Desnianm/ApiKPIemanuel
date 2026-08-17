<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_jenis_kategori', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kpi_jenis_id')
                  ->constrained('kpi_jenis')
                  ->onDelete('cascade');

            $table->foreignId('kategori_id')
                  ->constrained('kategori_unit_bisnis')
                  ->onDelete('cascade');

            $table->timestamps();

            // 1 KPI tidak boleh ke mapping dobel ke kategori yang sama
            $table->unique(['kpi_jenis_id', 'kategori_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_jenis_kategori');
    }
};