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
    Schema::create('form_fields', function (Blueprint $table) {
        $table->id();
        $table->foreignId('form_template_id')->constrained('form_templates')->onDelete('cascade');
        $table->foreignId('kpi_template_id')->nullable()->constrained('kpi_templates')->onDelete('set null');
        $table->string('label', 150);
        $table->enum('tipe', ['text', 'number', 'date', 'select', 'textarea']);
        $table->json('options')->nullable()->comment('Untuk tipe select, isi pilihan');
        $table->boolean('wajib')->default(false);
        $table->integer('urutan')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('form_fields');
}
};
