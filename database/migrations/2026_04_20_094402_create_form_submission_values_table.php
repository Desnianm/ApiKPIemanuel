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
    Schema::create('form_submission_values', function (Blueprint $table) {
        $table->id();
        $table->foreignId('form_submission_id')->constrained('form_submissions')->onDelete('cascade');
        $table->foreignId('form_field_id')->constrained('form_fields')->onDelete('cascade');
        $table->text('nilai')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('form_submission_values');
}
};
