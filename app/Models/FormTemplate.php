<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_templates';

    protected $fillable = [
        'unit_bisnis_id',
        'kpi_template_id', // FK baru — null berarti form standalone
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    // Relasi balik ke kpi_template (1-to-1)
    public function kpiTemplate()
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    // Field-field dalam form, diurutkan by urutan
    public function formFields()
    {
        return $this->hasMany(FormField::class, 'form_template_id')->orderBy('urutan');
    }

    public function formSubmissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_template_id');
    }
}