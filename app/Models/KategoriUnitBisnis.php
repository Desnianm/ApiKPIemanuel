<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriUnitBisnis extends Model
{
    use HasFactory;

    protected $table = 'kategori_unit_bisnis';

    protected $fillable = [
        'nama',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unitBisnis()
    {
        return $this->hasMany(UnitBisnis::class, 'kategori_id');
    }

    public function bobotKategoriPeriode()
    {
        return $this->hasMany(BobotKategoriPeriode::class, 'kategori_id');
    }


    public function kpiJenis()
    {
        return $this->belongsToMany(
            KpiJenis::class,
            'kpi_jenis_kategori',
            'kategori_id',
            'kpi_jenis_id'
        );
    }
}

