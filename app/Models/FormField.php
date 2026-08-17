<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $table = 'form_fields';

    protected $fillable = [
        'form_template_id',
        'kpi_template_id',
        'is_kpi_field',
        'label',
        'tipe',
        'options',
        'wajib',
        'urutan',
    ];

    protected $casts = [
        'options'      => 'array',
        'wajib'        => 'boolean',
        'is_kpi_field' => 'boolean',
    ];

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    public function kpiTemplate()
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }
}