<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiTemplate extends Model
{
    use HasFactory;

    protected $table = 'kpi_templates';

    protected $fillable = [
        'unit_bisnis_id',
        'nama',
        'kategori',
        'satuan',
        'deskripsi',
    ];

    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    public function kpiPeriods()
    {
        return $this->hasMany(KpiPeriod::class, 'kpi_template_id');
    }
}