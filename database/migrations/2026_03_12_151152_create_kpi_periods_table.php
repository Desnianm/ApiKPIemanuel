<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kpi_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis')->onDelete('cascade');
            $table->foreignId('kpi_template_id')->constrained('kpi_templates')->onDelete('cascade');
            $table->tinyInteger('periode_bulan');
            $table->year('periode_tahun');
            $table->decimal('target', 15, 2);
            $table->decimal('realisasi', 15, 2)->default(0);
            $table->enum('status', ['merah', 'kuning', 'hijau'])->nullable();
            $table->decimal('threshold_hijau', 5, 2);
            $table->decimal('threshold_kuning', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_periods');
    }
};
