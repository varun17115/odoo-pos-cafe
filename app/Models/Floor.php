<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    protected $fillable = ['name'];

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class, 'floor_id');
    }

    protected static function booted(): void
    {
        // When a floor is created, seed 5 default tables
        static::created(function (Floor $floor) {
            $count = $floor->tables()->count();
            for ($i = 1; $i <= 5; $i++) {
                $floor->tables()->create([
                    'number' => (string)(100 + $count + $i),
                    'seats'  => 4,
                    'status' => 'vacant',
                ]);
            }
        });
    }
}
