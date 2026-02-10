<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    protected $fillable = ['kelas_id', 'nama_rombel', 'guru_id', 'tahun_ajaran_id'];

    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }

    public function guru() {
        return $this->belongsTo(Guru::class);
    }

    public function tahunAjaran() {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function siswas()
{
    // 'id_rombel' adalah nama kolom di tabel siswas yang menyambung ke rombel
    return $this->hasMany(Siswa::class, 'id_rombel'); 
}

    public function jurusan()
{
    return $this->belongsTo(Jurusan::class, 'jurusan_id');
}
}