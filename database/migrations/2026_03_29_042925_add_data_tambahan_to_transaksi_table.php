<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->decimal('omset', 15, 2)->nullable()->after('nominal');
            $table->json('data_tambahan')->nullable()->after('keterangan');
            $table->string('bukti_bayar', 255)->nullable()->after('data_tambahan');
            $table->string('booking_id', 100)->nullable()->after('bukti_bayar');
            $table->string('status_transaksi', 50)->nullable()->after('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['omset', 'data_tambahan', 'bukti_bayar', 'booking_id', 'status_transaksi']);
        });
    }
};
