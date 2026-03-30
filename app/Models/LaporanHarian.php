<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHarian extends Model
{
    use HasFactory;

    protected $table = 'laporan_harian';

    protected $fillable = [
        'unit_bisnis_id',
        'user_id',
        'tanggal',
        'aktivitas',
        'leads_masuk',
        'closing_deal',
        'catatan',
    ];

    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}