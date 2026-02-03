<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'tahun',
        'semester',
        'is_active',
    ];

    /**
     * Casting is_active menjadi boolean agar mudah dicek (true/false)
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk mendapatkan tahun ajaran yang sedang aktif saja
     * Penggunaan di Controller: TahunAjaran::active()->first();
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}