<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_jenis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique(); // revenue occupancy dst
            $table->string('nama', 150); // revenue tingkat hunian
            $table->enum('kategori', [
                'sales', 'operasional', 'keuangan', 'sdm', 'lainnya'
            ]);
            $table->enum('satuan_default', [
                'rupiah', 'persen', 'unit', 'malam', 'lainnya'
            ]);
            $table->enum('formula_type', [
                'sum',
                'average',
                'count',
                'last_value'
            ]);
            // sum akumulasi tambah terus buat revenue, average rata rata dari semua submission, count hitung jumlah submission abaikan nilai, last value timpa langsung buat tingkat hunian
            // field definitions blueprint field yang otomatis dibuat saat admin pilih jenis ini, contoh label nilai deal tipe number wajib true
            $table->json('field_definitions');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_jenis');
    }
};