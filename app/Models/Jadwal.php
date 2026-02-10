<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
    'rombel_id', 
    'mapel_id', 
    'guru_id', 
    'sesi_id', // Pastikan ini ada
    'hari', 
    'jam_mulai', 
    'jam_selesai'
];

// Tambahkan relasi agar nanti bisa dipanggil di View
public function sesi()
{
    return $this->belongsTo(Sesi::class);
}

    public function rombel() { return $this->belongsTo(Rombel::class); }
    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function guru() { return $this->belongsTo(Guru::class); }
}