<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianSikap extends Model
{
    protected $fillable = [
        'periode_id',
        'siswa_id',
        'penilai_id',
        'tanggung_jawab',
        'kejujuran',
        'sopan_santun',
        'kemandirian',
        'kerja_sama',
        'catatan'
    ];

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class, 'siswa_id');
    }

    public function periode()
    {
        return $this->belongsTo(\App\Models\Periode::class, 'periode_id');
    }

    public function penilai()
    {
        return $this->belongsTo(\App\Models\User::class, 'penilai_id');
    }
}
