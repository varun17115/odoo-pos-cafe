<?php

use App\Http\Controllers\CustomerDisplayController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosConfigController;
use App\Http\Controllers\PosTerminalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Models\PosConfig;
use App\Models\PosSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return match(auth()->user()->role) {
        'chef'    => redirect()->route('kitchen.display'),
        'cashier' => redirect()->route('pos.terminal'),
        default   => redirect()->route('dashboard'),
    };
});

Route::get('/dashboard', function () {
    $activeConfig = PosConfig::where('is_active', true)->first();
    $openSession  = \App\Models\PosSession::where('status', 'open')->latest()->first();
    $lastSession  = \App\Models\PosSession::where('status', 'closed')->latest()->first();

    // Analytics — default to today
    $period    = request('period', 'today');
    $startDate = match($period) {
        'week'    => now()->startOfWeek(),
        'month'   => now()->startOfMonth(),
        'year'    => now()->startOfYear(),
        default   => now()->startOfDay(),
    };

    $paidOrders = \App\Models\Order::where('status', 'paid')
        ->where('created_at', '>=', $startDate)
        ->with('items.product.categories')
        ->get();

    $prevStart = match($period) {
        'week'  => now()->subWeek()->startOfWeek(),
        'month' => now()->subMonth()->startOfMonth(),
        'year'  => now()->subYear()->startOfYear(),
        default => now()->subDay()->startOfDay(),
    };
    $prevEnd = match($period) {
        'week'  => now()->subWeek()->endOfWeek(),
        'month' => now()->subMonth()->endOfMonth(),
        'year'  => now()->subYear()->endOfYear(),
        default => now()->subDay()->endOfDay(),
    };
    $prevOrders = \App\Models\Order::where('status', 'paid')
        ->whereBetween('created_at', [$prevStart, $prevEnd])
        ->get();

    $totalOrders   = $paidOrders->count();
    $totalRevenue  = $paidOrders->sum('total');
    $avgOrder      = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    $prevRevenue   = $prevOrders->sum('total');
    $prevCount     = $prevOrders->count();
    $prevAvg       = $prevCount > 0 ? $prevOrders->sum('total') / $prevCount : 0;

    $revChange  = $prevRevenue > 0 ? round(($totalRevenue - $prevRevenue) / $prevRevenue * 100) : 0;
    $cntChange  = $prevCount   > 0 ? round(($totalOrders  - $prevCount)   / $prevCount   * 100) : 0;
    $avgChange  = $prevAvg     > 0 ? round(($avgOrder     - $prevAvg)     / $prevAvg     * 100) : 0;

    // Sales chart data — group by hour (today) or day
    $salesChart = $paidOrders->groupBy(function ($o) use ($period) {
        return $period === 'today' ? $o->created_at->format('H:00') : $o->created_at->format('d M');
    })->map(fn($g) => round($g->sum('total'), 2))->sortKeys();

    // Top products
    $topProducts = \App\Models\OrderItem::whereHas('order', fn($q) => $q->where('status','paid')->where('created_at','>=',$startDate))
        ->selectRaw('name, SUM(quantity) as qty, SUM(subtotal) as revenue')
        ->groupBy('name')->orderByDesc('revenue')->limit(5)->get();

    // Top categories
    $topCategories = \App\Models\Category::withCount(['productsPivot as revenue' => function($q) use ($startDate) {
        $q->join('order_items','order_items.product_id','products.id')
          ->join('orders','orders.id','order_items.order_id')
          ->where('orders.status','paid')
          ->where('orders.created_at','>=',$startDate)
          ->select(\DB::raw('SUM(order_items.subtotal)'));
    }])->orderByDesc('revenue')->limit(5)->get();

    // Top orders (highest total)
    $topOrders = \App\Models\Order::where('status','paid')
        ->where('created_at','>=',$startDate)
        ->with('table')
        ->orderByDesc('total')
        ->limit(5)->get();

    return view('dashboard', compact(
        'activeConfig','openSession','lastSession',
        'period','totalOrders','totalRevenue','avgOrder',
        'revChange','cntChange','avgChange',
        'salesChart','topProducts','topCategories','topOrders'
    ));
})->middleware(['auth', 'role:admin'])->name('dashboard');

Route::get('/dashboard/export', function () {
    $period    = request('period', 'today');
    $format    = request('format', 'csv');
    $startDate = match($period) {
        'week'  => now()->startOfWeek(),
        'month' => now()->startOfMonth(),
        'year'  => now()->startOfYear(),
        default => now()->startOfDay(),
    };

    $orders = \App\Models\Order::where('status', 'paid')
        ->where('created_at', '>=', $startDate)
        ->with('table', 'items')
        ->orderByDesc('created_at')
        ->get();

    $filename = 'sales_' . $period . '_' . now()->format('Ymd');
    $headers  = ['Order #', 'Table', 'Items', 'Subtotal', 'Tax', 'Total', 'Payment', 'Date'];
    $rows     = $orders->map(fn($o) => [
        '#'.$o->id,
        $o->table ? 'T-'.$o->table->number : '—',
        $o->items->count(),
        '₹'.number_format($o->subtotal, 2),
        '₹'.number_format($o->tax, 2),
        '₹'.number_format($o->total, 2),
        $o->payment_method ?? '—',
        $o->created_at->format('d M Y H:i'),
    ]);

    $csv = implode(',', $headers) . "\n";
    foreach ($rows as $row) {
        $csv .= implode(',', $row) . "\n";
    }

    return response($csv, 200, [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename={$filename}.csv",
    ]);
})->middleware(['auth', 'role:admin'])->name('dashboard.export');

Route::middleware('auth')->group(function () {

    // Profile (all roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---- ADMIN ONLY ----
    Route::middleware('role:admin')->group(function () {

        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Settings / POS Configs
        Route::get('/settings', [PosConfigController::class, 'index'])->name('settings.index');
        Route::post('/settings', [PosConfigController::class, 'store'])->name('settings.store');
        Route::put('/settings/{posConfig}', [PosConfigController::class, 'update'])->name('settings.update');
        Route::post('/settings/{posConfig}/activate', [PosConfigController::class, 'activate'])->name('settings.activate');
        Route::delete('/settings/{posConfig}', [PosConfigController::class, 'destroy'])->name('settings.destroy');
        Route::get('/settings/{posConfig}/qr', [PosConfigController::class, 'downloadQr'])->name('settings.qr-download');

        // Products & Categories
        Route::resource('products', ProductController::class);
        Route::delete('/products-bulk', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Floors & Tables
        Route::get('/floors', [FloorController::class, 'index'])->name('floors.index');
        Route::post('/floors', [FloorController::class, 'store'])->name('floors.store');
        Route::put('/floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
        Route::delete('/floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');
        Route::post('/floors/{floor}/tables', [FloorController::class, 'storeTable'])->name('floors.tables.store');
        Route::put('/floors/{floor}/tables/{table}', [FloorController::class, 'updateTable'])->name('floors.tables.update');
        Route::delete('/floors/{floor}/tables/{table}', [FloorController::class, 'destroyTable'])->name('floors.tables.destroy');
        Route::delete('/floors/{floor}/tables-bulk', [FloorController::class, 'bulkDestroyTables'])->name('floors.tables.bulk-destroy');
        Route::post('/floors/{floor}/tables-bulk-status', [FloorController::class, 'bulkUpdateStatus'])->name('floors.tables.bulk-status');

    }); // end admin only

    // ---- ADMIN + CASHIER ----
    Route::middleware('role:admin,cashier')->group(function () {

        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // Customer Display
        Route::get('/customer-display', [CustomerDisplayController::class, 'show'])->name('customer.display');
        Route::get('/customer-display/state', [CustomerDisplayController::class, 'state'])->name('customer.display.state');
        Route::post('/customer-display/push', [CustomerDisplayController::class, 'push'])->name('customer.display.push');

        // POS Session
        Route::post('/pos/session/open', [SessionController::class, 'open'])->name('pos.session.open');
        Route::post('/pos/session/{session}/close', [SessionController::class, 'close'])->name('pos.session.close');

        // POS Terminal
        Route::get('/pos/terminal', [PosTerminalController::class, 'index'])->name('pos.terminal');
        Route::get('/pos/floor/{floor}', [PosTerminalController::class, 'floor'])->name('pos.floor');
        Route::get('/pos/products', [PosTerminalController::class, 'products'])->name('pos.products');

        // Orders
        Route::get('/pos/orders', [OrderController::class, 'index'])->name('pos.orders');
        Route::get('/pos/ordersIndex', [OrderController::class, 'indexOrder'])->name('pos.indexOrder');
        Route::get('/pos/payments', [OrderController::class, 'payments'])->name('pos.payments');
        Route::post('/pos/orders', [OrderController::class, 'store'])->name('pos.orders.store');
        Route::post('/pos/orders/bulk-draft', [OrderController::class, 'bulkDraft'])->name('pos.orders.bulk-draft');
        Route::post('/pos/orders/bulk-delete', [OrderController::class, 'bulkDelete'])->name('pos.orders.bulk-delete');
        Route::get('/pos/orders/{order}', [OrderController::class, 'show'])->name('pos.orders.show');
        Route::post('/pos/orders/{order}/items', [OrderController::class, 'addItem'])->name('pos.orders.items.add');
        Route::delete('/pos/orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('pos.orders.items.remove');
        Route::post('/pos/orders/{order}/sync', [OrderController::class, 'syncItems'])->name('pos.orders.sync');
        Route::post('/pos/orders/{order}/send', [OrderController::class, 'send'])->name('pos.orders.send');
        Route::post('/pos/orders/{order}/draft', [OrderController::class, 'draft'])->name('pos.orders.draft');
        Route::post('/pos/orders/{order}/pay', [OrderController::class, 'pay'])->name('pos.orders.pay');

    }); // end admin+cashier

    // ---- ALL ROLES (admin + cashier + chef) ----
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.display');
    Route::get('/kitchen/orders', [KitchenController::class, 'orders'])->name('kitchen.orders');
    Route::post('/kitchen/orders/{order}/items/{item}/toggle', [KitchenController::class, 'toggleItem'])->name('kitchen.item.toggle');
    Route::post('/kitchen/orders/{order}/ready', [KitchenController::class, 'markReady'])->name('kitchen.order.ready');
    Route::post('/kitchen/orders/{order}/preparing', [KitchenController::class, 'markPreparing'])->name('kitchen.order.preparing');

}); // end auth

require __DIR__.'/auth.php';

// Public mobile ordering — no auth required
Route::get('/s/{token}', [\App\Http\Controllers\MobileOrderController::class, 'landing'])->name('mobile.landing');
Route::get('/s/{token}/menu', [\App\Http\Controllers\MobileOrderController::class, 'menu'])->name('mobile.menu');
Route::get('/s/{token}/product/{product}', [\App\Http\Controllers\MobileOrderController::class, 'product'])->name('mobile.product');
Route::post('/s/{token}/order', [\App\Http\Controllers\MobileOrderController::class, 'placeOrder'])->name('mobile.order.place');
Route::get('/s/{token}/order/{orderId}/confirmed', [\App\Http\Controllers\MobileOrderController::class, 'confirmed'])->name('mobile.order.confirmed');
Route::get('/s/{token}/orders', [\App\Http\Controllers\MobileOrderController::class, 'history'])->name('mobile.order.history');
Route::get('/s/{token}/order/{orderId}/status', [\App\Http\Controllers\MobileOrderController::class, 'status'])->name('mobile.order.status');
Route::post('/s/{token}/order/{orderId}/pay', [\App\Http\Controllers\MobileOrderController::class, 'mobilePayOrder'])->name('mobile.order.pay');
