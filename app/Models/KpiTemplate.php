<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi_templates';

    protected $fillable = [
        'unit_bisnis_id',
        'kpi_jenis_id', 
        'nama',
        'kategori',
        'satuan',
        'deskripsi',
        'cap_max_persen',
    ];

    protected $casts = [
        'cap_max_persen' => 'float',
    ];

    // relasi ke unit bisnis
    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    // relasi ke katalog KPI
    public function kpiJenis()
    {
        return $this->belongsTo(KpiJenis::class, 'kpi_jenis_id');
    }

    // relasi 1 to 1 ke form template yang auto generated
    public function formTemplate()
    {
        return $this->hasOne(FormTemplate::class, 'kpi_template_id');
    }

    // relasi ke semua periode KPI
    public function kpiPeriods()
    {
        return $this->hasMany(KpiPeriod::class, 'kpi_template_id');
    }
}