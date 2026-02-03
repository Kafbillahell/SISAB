<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaRombel extends Model
{
    use HasFactory;

    protected $fillable = ['rombel_id', 'siswa_id'];

    public function rombel() {
        return $this->belongsTo(Rombel::class);
    }

    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }
}