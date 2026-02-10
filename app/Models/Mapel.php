<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $fillable = ['nama_mapel', 'kode_mapel'];

    public function jadwals()
{
    return $this->hasMany(Jadwal::class, 'mapel_id');
}
}