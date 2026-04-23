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
    Schema::create('form_submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('form_template_id')->constrained('form_templates')->onDelete('cascade');
        $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('form_submissions');
}
};
