<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotKategoriPeriode extends Model
{
    use HasFactory;

    protected $table = 'bobot_kategori_periode';

    protected $fillable = [
        'kategori_id',
        'periode',
        'bobot_persen',
        'ditetapkan_oleh',
        'tanggal_ditetapkan',
    ];

    protected $casts = [
        'periode'            => 'integer',
        'bobot_persen'       => 'float',
        'tanggal_ditetapkan' => 'datetime',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriUnitBisnis::class, 'kategori_id');
    }

    public function ditetapkanOleh()
    {
        return $this->belongsTo(User::class, 'ditetapkan_oleh');
    }
}