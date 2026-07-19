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
        'nilai_min', // untuk formula range
        'nilai_max', // untuk formula range
        'total_milestone', // untuk formula binary
        'is_capped', // batasi maksimal 100%
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
}