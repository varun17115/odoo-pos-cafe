<?php

namespace App\Http\Controllers;

use App\Models\PosConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerDisplayController extends Controller
{
    private const CACHE_KEY = 'customer_display_state';
    private const CACHE_TTL = 3600; // 1 hour

    public function show()
    {
        $config = PosConfig::where('is_active', true)->first();
        return view('customer-display.show', compact('config'));
    }

    // Terminal pushes current order state here
    public function push(Request $request)
    {
        $request->validate([
            'scene'  => 'required|in:idle,order,payment,thankyou',
            'order'  => 'nullable|array',
        ]);

        Cache::put(self::CACHE_KEY, [
            'scene'      => $request->scene,
            'order'      => $request->order,
            'updated_at' => now()->toISOString(),
        ], self::CACHE_TTL);

        return response()->json(['ok' => true]);
    }

    // Customer display polls this
    public function state()
    {
        $state = Cache::get(self::CACHE_KEY, [
            'scene'      => 'idle',
            'order'      => null,
            'updated_at' => now()->toISOString(),
        ]);

        return response()->json($state);
    }
}
