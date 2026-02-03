<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = ['jadwal_id', 'siswa_id', 'waktu_scan', 'status', 'keterangan'];

    public function jadwal() { return $this->belongsTo(Jadwal::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}