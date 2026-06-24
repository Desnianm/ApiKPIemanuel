<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_templates', function (Blueprint $table) {
            // Relasi 1-to-1 eksplisit antara kpi_template dan form_template
            // unique() memastikan 1 kpi_template hanya punya 1 form_template
            $table->foreignId('kpi_template_id')
                  ->nullable()
                  ->unique()
                  ->after('unit_bisnis_id')
                  ->constrained('kpi_templates')
                  ->onDelete('cascade'); 
            // cascade: kalau kpi_template dihapus, form_template-nya ikut terhapus otomatis

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('form_templates', function (Blueprint $table) {
            $table->dropForeign(['kpi_template_id']);
            $table->dropUnique(['kpi_template_id']);
            $table->dropColumn('kpi_template_id');
            $table->dropSoftDeletes();
        });
    }
};