<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiJenis extends Model
{
    use HasFactory;

    protected $table = 'kpi_jenis';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'satuan_default',
        'formula_type',
        'field_definitions',
        'nilai_min', 
        'nilai_max',
        'total_milestone', 
        'is_capped', 
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'field_definitions' => 'array',
        'is_active'         => 'boolean',
        'is_capped'         => 'boolean',
        'nilai_min'         => 'float',
        'nilai_max'         => 'float',
        'total_milestone'   => 'integer',
    ];

    public function kpiTemplates()
    {
        return $this->hasMany(KpiTemplate::class, 'kpi_jenis_id');
    }

    // KPI ini bisa relevan untuk beberapa kategori unit bisnis sekaligus
    public function kategoriUnitBisnis()
    {
        return $this->belongsToMany(
            KategoriUnitBisnis::class,
            'kpi_jenis_kategori',
            'kpi_jenis_id',
            'kategori_id'
        );
    }
}