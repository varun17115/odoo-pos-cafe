<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\PosConfig;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class MobileOrderController extends Controller
{
    private function resolveTable(string $token): ?RestaurantTable
    {
        return RestaurantTable::where('qr_token', $token)->first();
    }

    private function activeSession(): ?PosSession
    {
        return PosSession::where('status', 'open')->latest()->first();
    }

    public function landing(string $token)
    {
        $table  = $this->resolveTable($token);
        $config = PosConfig::where('is_active', true)->where('self_ordering', true)->first();
        return view('mobile.landing', compact('token', 'table', 'config'));
    }

    public function menu(string $token)
    {
        $table      = $this->resolveTable($token);
        $config     = PosConfig::where('is_active', true)->first();
        $categories = Category::orderBy('sort_order')->orderBy('id')->get();
        $products   = Product::with('categories', 'variants')->get();
        return view('mobile.menu', compact('token', 'table', 'config', 'categories', 'products'));
    }

    public function product(string $token, Product $product)
    {
        $table  = $this->resolveTable($token);
        $config = PosConfig::where('is_active', true)->first();
        $product->load('categories', 'variants');
        return view('mobile.product', compact('token', 'table', 'config', 'product'));
    }

    public function placeOrder(Request $request, string $token)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.name'       => 'required|string',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $table   = $this->resolveTable($token);
        $session = $this->activeSession();

        $order = Order::create([
            'pos_session_id' => $session?->id,
            'table_id'       => $table?->id,
            'status'         => 'preparing',
            'notes'          => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $product  = Product::find($item['product_id']);
            $taxRate  = $product ? (float) $product->tax : 0;
            $price    = (float) $item['price'];
            $qty      = (int) $item['quantity'];
            $subtotal = round($price * $qty, 2);
            $order->items()->create([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'name'       => $item['name'],
                'price'      => $price,
                'quantity'   => $qty,
                'subtotal'   => $subtotal,
                'tax_rate'   => $taxRate,
                'tax_amount' => round($subtotal * $taxRate / 100, 2),
            ]);
        }

        $order->load('items');
        $order->recalculate();

        if ($table) {
            $table->update(['status' => 'occupied']);
        }

        // Store order id in session for tracking
        session()->push('mobile_orders_' . $token, $order->id);

        return redirect()->route('mobile.order.confirmed', [$token, $order->id]);
    }

    public function confirmed(string $token, int $orderId)
    {
        $table  = $this->resolveTable($token);
        $order  = Order::with('items')->findOrFail($orderId);
        $config = PosConfig::where('is_active', true)->first();
        return view('mobile.confirmed', compact('token', 'table', 'order', 'config'));
    }

    public function history(string $token)
    {
        $table    = $this->resolveTable($token);
        $orderIds = session()->get('mobile_orders_' . $token, []);
        $orders   = Order::whereIn('id', $orderIds)->latest()->get();
        $config   = PosConfig::where('is_active', true)->first();
        return view('mobile.history', compact('token', 'table', 'orders', 'config'));
    }

    public function status(string $token, int $orderId)
    {
        $order = Order::findOrFail($orderId);
        return response()->json(['status' => $order->status]);
    }

    public function mobilePayOrder(Request $request, string $token, int $orderId)
    {
        $request->validate(['payment_method' => 'required|in:cash,card,upi']);
        $order = Order::findOrFail($orderId);

        $order->update([
            'status'         => 'paid',
            'payment_method' => $request->payment_method,
        ]);

        if ($order->table_id) {
            RestaurantTable::where('id', $order->table_id)->update(['status' => 'vacant']);
        }

        if ($order->posSession) {
            $totalSales = $order->posSession->orders()->where('status', 'paid')->sum('total');
            $order->posSession->update(['total_sales' => $totalSales]);
        }

        return response()->json(['ok' => true, 'status' => 'paid']);
    }
}
