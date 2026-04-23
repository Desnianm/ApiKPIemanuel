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
    Schema::create('form_templates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis')->onDelete('cascade');
        $table->string('nama', 150);
        $table->text('deskripsi')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('form_templates');
}
};
