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
        'usage_type',
        'valid_minutes',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
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
