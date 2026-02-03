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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}