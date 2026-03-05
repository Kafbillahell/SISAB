<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = ['jadwal_id', 'siswa_id', 'waktu_scan', 'status', 'keterangan', 'tanggal'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->tanggal) && !empty($model->waktu_scan)) {
                $model->tanggal = Carbon::parse($model->waktu_scan)->format('Y-m-d');
            }
            // if waktu_scan empty, set both waktu_scan and tanggal
            if (empty($model->waktu_scan) && !empty($model->tanggal)) {
                $model->waktu_scan = Carbon::parse($model->tanggal)->startOfDay();
            }
        });
    }

    public function jadwal() { return $this->belongsTo(Jadwal::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}