<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollReminder extends Model
{
    use HasFactory;

    protected $table = 'payroll_reminders';

    protected $fillable = [
        'user_id',
        'periode_bulan',
        'periode_tahun',
        'gaji_pokok',
        'tunjangan',
        'potongan',
        'total_gaji',
        'sudah_diingatkan',
        'tanggal_reminder',
        'catatan',
    ];

    protected $casts = [
        'sudah_diingatkan' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Hitung total gaji otomatis
    public function hitungTotalGaji(): float
    {
        return $this->gaji_pokok + $this->tunjangan - $this->potongan;
    }
}