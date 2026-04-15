<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'voucher_id',
        'redeemed_at',
        'used_at',
        'is_used',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
