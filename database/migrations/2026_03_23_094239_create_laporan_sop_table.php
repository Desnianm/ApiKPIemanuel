<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_sop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('deskripsi')->comment('Deskripsi kegiatan SOP yang dilakukan');
            $table->string('foto', 255)->nullable()->comment('Path foto bukti');
            $table->string('lokasi', 255)->nullable()->comment('GPS/lokasi saat foto diambil');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Dinilai manual oleh owner');
            $table->text('catatan_owner')->nullable()->comment('Catatan dari owner saat review');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Owner yang mereview');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_sop');
    }
};