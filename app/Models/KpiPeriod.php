<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPeriod extends Model
{
    use HasFactory;

    protected $table = 'kpi_periods';

    protected $fillable = [
        'unit_bisnis_id',
        'kpi_template_id',
        'periode_bulan',
        'periode_tahun',
        'target',
        'realisasi',
        'status',
        'threshold_hijau',
        'threshold_kuning',
    ];

    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }

    public function kpiTemplate()
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    // Hitung status otomatis merah/kuning/hijau
    public function hitungStatus(): string
    {
        if ($this->target == 0) return 'merah';

        $persentase = ($this->realisasi / $this->target) * 100;

        if ($persentase >= $this->threshold_hijau) {
            return 'hijau';
        } elseif ($persentase >= $this->threshold_kuning) {
            return 'kuning';
        } else {
            return 'merah';
        }
    }
}