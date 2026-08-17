<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'unit_bisnis_id',
        'user_id',
        'booking_key',
        'nama_customer',
        'no_hp',
        'tipe_transaksi',
        'tanggal_checkin',
        'tanggal_checkout',
        'nominal',
        'omset',
        'keterangan',
        'data_tambahan',
        'bukti_bayar',
        'booking_id',
        'status_transaksi',
    ];

    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // generate booking key otomatis
    public static function generateBookingKey(string $nama, string $noHp, string $tanggal): string
    {
        $noHp4digit = substr($noHp, -4);
        return strtoupper(md5($nama . $noHp4digit . $tanggal));
    }

    // cek duplikat booking key
    public static function isDuplikat(string $bookingKey): bool
    {
        return self::where('booking_key', $bookingKey)->exists();
    }
}