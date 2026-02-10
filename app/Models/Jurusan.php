<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    // Tambahkan baris ini
    protected $fillable = [
        'nama_jurusan',
        'kode_jurusan',
    ];

    // Relasi ke Kelas (Opsional, tapi bagus untuk dimiliki)
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'jurusan_id');
    }

    // app/Models/Jurusan.php
public function rombels()
{
    return $this->hasMany(Rombel::class);
}
}