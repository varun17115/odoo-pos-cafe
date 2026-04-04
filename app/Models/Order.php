<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;

class Order extends Model
{
    protected $fillable = [
        'pos_session_id', 'table_id', 'customer_id', 'customer_name', 'status',
        'subtotal', 'tax', 'total', 'payment_method', 'notes',
    ];

    public function posSession(): BelongsTo
    {
        return $this->belongsTo(PosSession::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $tax      = $this->items->sum('tax_amount');
        $this->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $subtotal + $tax,
        ]);
    }
}
