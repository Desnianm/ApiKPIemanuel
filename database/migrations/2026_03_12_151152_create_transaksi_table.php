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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('booking_key', 100)->unique();
            $table->string('nama_customer', 100);
            $table->string('no_hp', 20);
            $table->enum('tipe_transaksi', ['booking_fee', 'dp', 'pelunasan', 'sewa_harian', 'penjualan', 'lainnya']);
            $table->date('tanggal_checkin')->nullable();
            $table->date('tanggal_checkout')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
