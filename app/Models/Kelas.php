<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas'; // Nama tabel di migrasi
    protected $fillable = ['nama_kelas', 'tingkat'];
}
