<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'point_cost',
        'quantity',
        'used',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];

    public function studentVouchers()
    {
        return $this->hasMany(StudentVoucher::class);
    }

    public function getAvailableCount()
    {
        return $this->quantity - $this->used;
    }
}
