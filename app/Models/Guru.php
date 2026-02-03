<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Menentukan nama tabel (opsional jika nama tabelnya 'gurus')
    protected $table = 'gurus';

    // Kolom yang boleh diisi melalui input form
    protected $fillable = [
        'user_id',
        'nip',
        'nama_guru',
        'jenis_kelamin',
    ];

    /**
     * Relasi ke model User (Setiap Guru terhubung ke satu User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}