<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function activeSession(): ?PosSession
    {
        return PosSession::where('status', 'open')->latest()->first();
    }

    private function buildItemData(array $item): array
    {
        $product  = Product::find($item['product_id']);
        $taxRate  = $product ? (float) $product->tax : 0;
        $price    = (float) $item['price'];
        $qty      = (int) $item['quantity'];
        $subtotal = round($price * $qty, 2);
        $taxAmt   = round($subtotal * $taxRate / 100, 2);

        return [
            'product_id' => $item['product_id'],
            'variant_id' => $item['variant_id'] ?? null,
            'name'       => $item['name'],
            'price'      => $price,
            'quantity'   => $qty,
            'subtotal'   => $subtotal,
            'tax_rate'   => $taxRate,
            'tax_amount' => $taxAmt,
        ];
    }

    private function withRelations(Order $order)
    {
        return $order->load('items', 'table.floor', 'customer', 'posSession');
    }

    public function index()
    {
        $session = $this->activeSession();
        $orders  = $session
            ? $session->orders()->with(['table.floor', 'items'])->orderBy('payment_method')->latest()->get()
            : collect();

        if (request()->wantsJson()) {
            return response()->json($orders);
        }

        return view('pos.orders', compact('session', 'orders'));
    }

    public function payments()
    {
        $orders = Order::with(['table.floor', 'items'])
            ->where('status', 'paid')
            ->whereNotNull('payment_method')
            ->orderBy('payment_method')
            ->latest()
            ->get();

        // Group by payment method, compute totals per group
        $grouped = $orders->groupBy('payment_method')->map(function ($group) {
            return [
                'orders' => $group,
                'total'  => $group->sum('total'),
            ];
        });

        $grandTotal = $orders->sum('total');

        return view('pos.payments', compact('grouped', 'grandTotal'));
    }

    public function indexOrder()
    {
        $session = $this->activeSession();
        $orders  = Order::with(['table.floor', 'items'])->orderBy('status')->latest()->get();

        if (request()->wantsJson()) {
            return response()->json($orders);
        }

        return view('pos.orders', compact('session', 'orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id'           => 'nullable|exists:tables,id',
            'customer_id'        => 'nullable|exists:customers,id',
            'customer_name'      => 'nullable|string|max:100',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.name'       => 'required|string',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'notes'              => 'nullable|string',
        ]);

        $session = $this->activeSession();
        if (!$session) {
            return response()->json(['error' => 'No active session'], 422);
        }

        // If customer_id given, use their name
        $customerName = $request->customer_name;
        if ($request->customer_id) {
            $customerName = Customer::find($request->customer_id)?->name ?? $customerName;
        }

        $order = Order::create([
            'pos_session_id' => $session->id,
            'table_id'       => $request->table_id,
            'customer_id'    => $request->customer_id,
            'customer_name'  => $customerName,
            'status'         => 'pending',
            'notes'          => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $order->items()->create($this->buildItemData($item));
        }

        $order->load('items');
        $order->recalculate();

        if ($request->table_id) {
            RestaurantTable::where('id', $request->table_id)->update(['status' => 'occupied']);
        }

        return response()->json($this->withRelations($order));
    }

    public function syncItems(Request $request, Order $order)
    {
        $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'customer_name'      => 'nullable|string|max:100',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.name'       => 'required|string',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'notes'              => 'nullable|string',
        ]);

        $customerName = $request->customer_name;
        if ($request->customer_id) {
            $customerName = Customer::find($request->customer_id)?->name ?? $customerName;
        }

        $order->update([
            'customer_id'   => $request->customer_id,
            'customer_name' => $customerName,
            'notes'         => $request->notes,
        ]);

        $order->items()->delete();
        foreach ($request->items as $item) {
            $order->items()->create($this->buildItemData($item));
        }

        $order->load('items');
        $order->recalculate();

        return response()->json($this->withRelations($order));
    }

    // public function payments()
    // {
    //     $orders = Order::with(['table.floor'])
    //         ->where('status', 'paid')
    //         ->orderBy('payment_method')
    //         ->latest()
    //         ->get();

    //     // Group by payment method, compute totals per group
    //     $grouped = $orders->groupBy(fn($o) => $o->payment_method ?? 'unknown')
    //         ->map(fn($group) => [
    //             'orders' => $group,
    //             'total'  => $group->sum('total'),
    //         ]);

    //     $grandTotal = $orders->sum('total');

    //     return view('pos.payments', compact('grouped', 'grandTotal'));
    // }

    public function show(Order $order)
    {
        return response()->json($this->withRelations($order));
    }

    public function addItem(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'name'       => 'required|string',
            'price'      => 'required|numeric|min:0',
            'quantity'   => 'required|integer|min:1',
        ]);

        $existing = $order->items()
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($existing) {
            $newQty    = $existing->quantity + $request->quantity;
            $subtotal  = round($existing->price * $newQty, 2);
            $taxAmount = round($subtotal * $existing->tax_rate / 100, 2);
            $existing->update([
                'quantity'   => $newQty,
                'subtotal'   => $subtotal,
                'tax_amount' => $taxAmount,
            ]);
        } else {
            $order->items()->create($this->buildItemData($request->all()));
        }

        $order->load('items');
        $order->recalculate();

        return response()->json($this->withRelations($order));
    }

    public function removeItem(Order $order, OrderItem $item)
    {
        $item->delete();
        $order->load('items');
        $order->recalculate();

        return response()->json($this->withRelations($order));
    }

    public function send(Request $request, Order $order)
    {
        $request->validate(['customer_name' => 'nullable|string|max:100']);

        $order->update([
            'status'        => 'preparing',
            'customer_name' => $request->customer_name ?? $order->customer_name,
        ]);

        return response()->json($this->withRelations($order));
    }

    public function draft(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        if ($order->table_id) {
            // Free the table only if no other active orders on it
            $hasOther = Order::where('table_id', $order->table_id)
                ->where('id', '!=', $order->id)
                ->whereNotIn('status', ['paid', 'cancelled'])
                ->exists();
            if (!$hasOther) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 'vacant']);
            }
        }

        return response()->json($this->withRelations($order));
    }

    public function bulkDraft(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:orders,id']);
        Order::whereIn('id', $request->ids)->update(['status' => 'cancelled']);
        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:orders,id']);
        // Only allow deleting draft (cancelled) orders
        Order::whereIn('id', $request->ids)->where('status', 'cancelled')->delete();
        return response()->json(['success' => true]);
    }

    public function pay(Request $request, Order $order)
    {
        $request->validate(['payment_method' => 'required|in:cash,card,upi']);

        $order->update([
            'status'         => 'paid',
            'payment_method' => $request->payment_method,
        ]);

        if ($order->table_id) {
            RestaurantTable::where('id', $order->table_id)->update(['status' => 'vacant']);
        }

        $session    = $order->posSession;
        $totalSales = $session->orders()->where('status', 'paid')->sum('total');
        $session->update(['total_sales' => $totalSales]);

        // Update customer total sales
        if ($order->customer_id) {
            Customer::find($order->customer_id)?->recalculateSales();
        }

        return response()->json($this->withRelations($order));
    }
}
