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
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'field_definitions' => 'array', // otomatis parse JSON jadi array PHP
        'is_active'         => 'boolean',
    ];

    // Satu jenis KPI bisa dipakai oleh banyak kpi_template
    public function kpiTemplates()
    {
        return $this->hasMany(KpiTemplate::class, 'kpi_jenis_id');
    }
}