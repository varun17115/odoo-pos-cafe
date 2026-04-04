<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        return view('kitchen.display');
    }

    // Poll endpoint — returns all active kitchen orders
    public function orders(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all | preparing | ready

        $query = Order::with(['items.product.categories', 'table.floor'])
            ->whereIn('status', ['preparing', 'ready'])
            ->latest();

        if ($filter === 'preparing') {
            $query->where('status', 'preparing');
        } elseif ($filter === 'ready') {
            $query->where('status', 'ready');
        }

        return response()->json($query->get());
    }

    // Toggle a single item done/undone
    public function toggleItem(Order $order, OrderItem $item)
    {
        $item->update(['done' => !$item->done]);

        // If all items are done → auto-set order to ready
        $allDone = $order->items()->where('done', false)->doesntExist();
        if ($allDone && $order->status === 'preparing') {
            $order->update(['status' => 'ready']);
        }

        // If an item is un-done and order was ready → back to preparing
        if (!$item->done && $order->status === 'ready') {
            $order->update(['status' => 'preparing']);
        }

        return response()->json([
            'item'   => $item->fresh(),
            'order'  => $order->fresh(['items.product.categories', 'table.floor']),
        ]);
    }

    // Manually mark order as ready
    public function markReady(Order $order)
    {
        $order->update(['status' => 'ready']);
        // Mark all items done
        $order->items()->update(['done' => true]);
        return response()->json($order->fresh(['items.product.categories', 'table.floor']));
    }

    // Mark order back to preparing
    public function markPreparing(Order $order)
    {
        $order->update(['status' => 'preparing']);
        return response()->json($order->fresh(['items.product.categories', 'table.floor']));
    }
}
