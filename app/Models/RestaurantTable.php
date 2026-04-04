<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantTable extends Model
{
    protected $table = 'tables';

    protected $fillable = ['floor_id', 'number', 'seats', 'status'];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'vacant'   => '#22c55e',
            'occupied' => '#ef4444',
            'reserved' => '#f59e0b',
            default    => '#6b7280',
        };
    }
}
