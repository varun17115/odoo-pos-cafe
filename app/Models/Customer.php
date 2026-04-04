<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone',
        'street1', 'street2', 'city', 'state', 'country',
        'total_sales',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function recalculateSales(): void
    {
        $this->update([
            'total_sales' => $this->orders()->where('status', 'paid')->sum('total'),
        ]);
    }
}
