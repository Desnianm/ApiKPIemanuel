<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bobot_kategori_periode', function (Blueprint $table) {
            $table->id();

            // mengikuti konvensi existing unit bisnis pakai kolom kategori id yang fk ke kategori unit bisnis bukan kategori bisnis id
            $table->foreignId('kategori_id')
                  ->constrained('kategori_unit_bisnis')
                  ->onDelete('cascade');

            // tahun periode contoh dua ribu dua puluh enam bisa dikembangkan ke bulan tahun nanti kalau memang dibutuhkan granularitas lebih kecil
            $table->unsignedSmallInteger('periode');

            $table->decimal('bobot_persen', 5, 2);

            // audit siapa yang menetapkan bobot ini
            $table->foreignId('ditetapkan_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamp('tanggal_ditetapkan')->nullable();

            $table->timestamps();

            // satu kategori hanya boleh punya satu bobot per periode
            $table->unique(['kategori_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_kategori_periode');
    }
};