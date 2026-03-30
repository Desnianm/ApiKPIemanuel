<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanSop extends Model
{
    use HasFactory;

    protected $table = 'laporan_sop';

    protected $fillable = [
        'unit_bisnis_id',
        'user_id',
        'tanggal',
        'deskripsi',
        'foto',
        'lokasi',
        'latitude',
        'longitude',
        'status',
        'catatan_owner',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'latitude'    => 'float',
        'longitude'   => 'float',
    ];

    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}