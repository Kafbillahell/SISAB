<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nisn',
        'nama_siswa',
        'jenis_kelamin',
        'face_embedding',
        'foto'
    ];

    // Otomatis mengubah JSON di DB menjadi Array di PHP
    protected $casts = [
        'face_embedding' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function presensis()
{
    // Ini memberitahu Laravel bahwa satu Siswa memiliki banyak data di tabel Presensi
    return $this->hasMany(Presensi::class, 'siswa_id');
}
}