<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi secara massal
    protected $fillable = [
        'hari', 
        'urutan', 
        'nama_sesi', 
        'jam_mulai', 
        'jam_selesai', 
        'is_istirahat'
    ];

    // Opsional: Jika Anda ingin memastikan tipe data is_istirahat selalu boolean
    protected $casts = [
        'is_istirahat' => 'boolean',
    ];
}