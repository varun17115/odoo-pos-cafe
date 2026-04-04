<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosConfig extends Model
{
    protected $fillable = [
        'name', 'is_active',
        'payment_cash', 'payment_card', 'payment_upi', 'upi_id',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'payment_cash' => 'boolean',
        'payment_card' => 'boolean',
        'payment_upi'  => 'boolean',
    ];
}
