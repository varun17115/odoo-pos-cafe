<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\PosConfig;
use App\Models\PosSession;
use App\Models\Product;

class PosTerminalController extends Controller
{
    public function index()
    {
        $session = PosSession::where('status', 'open')
            ->with('posConfig')
            ->latest()
            ->first();

        if (! $session) {
            return redirect()->route('dashboard')->with('error', 'No active session. Please open a session first.');
        }

        $floors    = Floor::with(['tables' => function ($q) {
            $q->orderBy('number');
        }])->get();

        $posConfig = $session->posConfig;

        return view('pos.terminal', compact('session', 'floors', 'posConfig'));
    }

    public function floor(Floor $floor)
    {
        $floor->load(['tables' => function ($q) {
            $q->orderBy('number');
        }]);

        // Attach active order id to each table
        $session = PosSession::where('status', 'open')->latest()->first();

        $tables = $floor->tables->map(function ($table) use ($session) {
            $activeOrder = null;
            if ($session) {
                $activeOrder = $session->orders()
                    ->where('table_id', $table->id)
                    ->whereNotIn('status', ['paid', 'cancelled'])
                    ->latest()
                    ->first();
            }
            return [
                'id'           => $table->id,
                'number'       => $table->number,
                'seats'        => $table->seats,
                'status'       => $table->status,
                'active_order' => $activeOrder ? $activeOrder->id : null,
            ];
        });

        return response()->json([
            'id'     => $floor->id,
            'name'   => $floor->name,
            'tables' => $tables,
        ]);
    }

    public function products()
    {
        $products = Product::with(['categories', 'variants'])
            ->get()
            ->map(function ($product) {
                return [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'price'       => $product->price,
                    'tax'         => $product->tax,
                    'image'       => $product->image ? asset('storage/' . $product->image) : null,
                    'categories'  => $product->categories->map(fn($c) => [
                        'id'    => $c->id,
                        'name'  => $c->name,
                        'color' => $c->color,
                    ]),
                    'variants'    => $product->variants->map(fn($v) => [
                        'id'    => $v->id,
                        'name'  => $v->name,
                        'price' => $v->price,
                    ]),
                ];
            });

        return response()->json($products);
    }
}
