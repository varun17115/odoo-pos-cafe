<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosConfig extends Model
{
    protected $fillable = [
        'name', 'is_active',
        'payment_cash', 'payment_card', 'payment_upi', 'upi_id',
        'self_ordering', 'self_ordering_type', 'self_ordering_token',
        'bg_color', 'bg_image_1', 'bg_image_2', 'bg_image_3',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'payment_cash'  => 'boolean',
        'payment_card'  => 'boolean',
        'payment_upi'   => 'boolean',
        'self_ordering' => 'boolean',
    ];
}
