<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSession extends Model
{
    protected $fillable = [
        'pos_config_id', 'opened_by', 'opened_at', 'closed_at', 'status', 'total_sales',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function posConfig(): BelongsTo
    {
        return $this->belongsTo(PosConfig::class);
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
