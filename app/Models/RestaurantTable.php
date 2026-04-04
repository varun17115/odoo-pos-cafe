<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantTable extends Model
{
    protected $table = 'tables';

    protected $fillable = ['floor_id', 'number', 'seats', 'status', 'qr_token'];

    protected static function booted(): void
    {
        static::creating(function ($table) {
            if (empty($table->qr_token)) {
                $table->qr_token = \Illuminate\Support\Str::random(8);
            }
        });
    }

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
