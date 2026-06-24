<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            // Flag pembeda:
            // true  = field dari katalog (auto-generated, TIDAK bisa dihapus admin)
            // false = field tambahan manual admin (BISA dihapus admin)
            $table->boolean('is_kpi_field')
                  ->default(false)
                  ->after('kpi_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('is_kpi_field');
        });
    }
};