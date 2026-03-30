<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitBisnis extends Model
{
    use HasFactory;

    protected $table = 'unit_bisnis';

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriUnitBisnis::class, 'kategori_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'unit_bisnis_id');
    }

    public function kpiTemplates()
    {
        return $this->hasMany(KpiTemplate::class, 'unit_bisnis_id');
    }

    public function kpiPeriods()
    {
        return $this->hasMany(KpiPeriod::class, 'unit_bisnis_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'unit_bisnis_id');
    }

    public function laporanHarian()
    {
        return $this->hasMany(LaporanHarian::class, 'unit_bisnis_id');
    }

    public function sop()
    {
        return $this->hasMany(Sop::class, 'unit_bisnis_id');
    }
}