<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Pastikan jurusan_id sudah ada di fillable
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan_id', // Tambahkan ini jika belum ada
    ];

    /**
     * Relasi ke model Jurusan
     */
    public function jurusan()
    {
        // Kelas "belongsTo" (milik) satu Jurusan
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}