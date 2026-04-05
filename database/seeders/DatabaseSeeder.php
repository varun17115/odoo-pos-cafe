<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Floor;
use App\Models\Order;
use App\Models\PosConfig;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────
        $admin = User::updateOrCreate(['email' => 'admin@restopos.com'], [
            'name'     => 'Admin User',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::updateOrCreate(['email' => 'cashier@restopos.com'], [
            'name'     => 'John Cashier',
            'password' => Hash::make('password'),
            'role'     => 'cashier',
        ]);

        User::updateOrCreate(['email' => 'chef@restopos.com'], [
            'name'     => 'Chef Marco',
            'password' => Hash::make('password'),
            'role'     => 'chef',
        ]);

        // ── POS Config ─────────────────────────────────────────
        $config = PosConfig::updateOrCreate(['name' => 'Main Counter'], [
            'is_active'    => true,
            'payment_cash' => true,
            'payment_card' => true,
            'payment_upi'  => true,
            'upi_id'       => 'restopos@ybl',
        ]);

        // ── Categories ─────────────────────────────────────────
        $cats = [
            ['name' => 'Quick Bites',  'color' => '#f97316', 'sort_order' => 1],
            ['name' => 'Main Course',  'color' => '#ef4444', 'sort_order' => 2],
            ['name' => 'Drinks',       'color' => '#3b82f6', 'sort_order' => 3],
            ['name' => 'Desserts',     'color' => '#a855f7', 'sort_order' => 4],
            ['name' => 'Pasta',        'color' => '#f59e0b', 'sort_order' => 5],
        ];

        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat['name']] = Category::updateOrCreate(
                ['name' => $cat['name']],
                ['color' => $cat['color'], 'sort_order' => $cat['sort_order']]
            );
        }

        // ── Products ───────────────────────────────────────────
        $products = [
            [
                'name' => 'Margherita Pizza', 'price' => 250, 'tax' => 5, 'unit' => 'Piece',
                'description' => 'Classic tomato and mozzarella pizza',
                'categories' => ['Quick Bites'],
                'variants' => [
                    ['attribute' => 'Size', 'name' => '6 inch',  'unit' => 'Piece', 'price' => 0],
                    ['attribute' => 'Size', 'name' => '8 inch',  'unit' => 'Piece', 'price' => 50],
                    ['attribute' => 'Size', 'name' => '12 inch', 'unit' => 'Piece', 'price' => 120],
                ],
            ],
            [
                'name' => 'Cheese Burger', 'price' => 180, 'tax' => 5, 'unit' => 'Piece',
                'description' => 'Juicy beef patty with cheddar cheese',
                'categories' => ['Quick Bites'],
                'variants' => [
                    ['attribute' => 'Size', 'name' => 'Regular', 'unit' => 'Piece', 'price' => 0],
                    ['attribute' => 'Size', 'name' => 'Large',   'unit' => 'Piece', 'price' => 40],
                ],
            ],
            [
                'name' => 'Grilled Chicken', 'price' => 320, 'tax' => 5, 'unit' => 'Piece',
                'description' => 'Herb-marinated grilled chicken breast',
                'categories' => ['Main Course'],
                'variants' => [],
            ],
            [
                'name' => 'Paneer Butter Masala', 'price' => 280, 'tax' => 5, 'unit' => 'Piece',
                'description' => 'Creamy tomato-based paneer curry',
                'categories' => ['Main Course'],
                'variants' => [],
            ],
            [
                'name' => 'Truffle Pasta', 'price' => 350, 'tax' => 18, 'unit' => 'Piece',
                'description' => 'Creamy pasta with truffle oil and parmesan',
                'categories' => ['Pasta'],
                'variants' => [
                    ['attribute' => 'Size', 'name' => 'Half',  'unit' => 'Piece', 'price' => 0],
                    ['attribute' => 'Size', 'name' => 'Full',  'unit' => 'Piece', 'price' => 80],
                ],
            ],
            [
                'name' => 'Cold Coffee', 'price' => 120, 'tax' => 0, 'unit' => 'ML',
                'description' => 'Chilled coffee with milk and ice',
                'categories' => ['Drinks'],
                'variants' => [
                    ['attribute' => 'Size', 'name' => '250ml', 'unit' => 'ML', 'price' => 0],
                    ['attribute' => 'Size', 'name' => '500ml', 'unit' => 'ML', 'price' => 60],
                ],
            ],
            [
                'name' => 'Fresh Lime Soda', 'price' => 80, 'tax' => 0, 'unit' => 'ML',
                'description' => 'Refreshing lime with soda water',
                'categories' => ['Drinks'],
                'variants' => [],
            ],
            [
                'name' => 'Mango Lassi', 'price' => 100, 'tax' => 0, 'unit' => 'ML',
                'description' => 'Thick mango yogurt drink',
                'categories' => ['Drinks'],
                'variants' => [],
            ],
            [
                'name' => 'Chocolate Lava Cake', 'price' => 160, 'tax' => 5, 'unit' => 'Piece',
                'description' => 'Warm chocolate cake with molten center',
                'categories' => ['Desserts'],
                'variants' => [],
            ],
            [
                'name' => 'Gulab Jamun', 'price' => 80, 'tax' => 0, 'unit' => 'Piece',
                'description' => 'Soft milk-solid dumplings in sugar syrup',
                'categories' => ['Desserts'],
                'variants' => [
                    ['attribute' => 'Pack', 'name' => '2 pcs', 'unit' => 'Piece', 'price' => 0],
                    ['attribute' => 'Pack', 'name' => '4 pcs', 'unit' => 'Piece', 'price' => 60],
                ],
            ],
            [
                'name' => 'French Fries', 'price' => 120, 'tax' => 5, 'unit' => 'Piece',
                'description' => 'Crispy golden fries with seasoning',
                'categories' => ['Quick Bites'],
                'variants' => [
                    ['attribute' => 'Size', 'name' => 'Small',  'unit' => 'Piece', 'price' => 0],
                    ['attribute' => 'Size', 'name' => 'Medium', 'unit' => 'Piece', 'price' => 30],
                    ['attribute' => 'Size', 'name' => 'Large',  'unit' => 'Piece', 'price' => 60],
                ],
            ],
            [
                'name' => 'Water Bottle', 'price' => 30, 'tax' => 0, 'unit' => 'Piece',
                'description' => '1 litre mineral water',
                'categories' => ['Drinks'],
                'variants' => [],
            ],
        ];

        $productModels = [];
        foreach ($products as $p) {
            $product = Product::updateOrCreate(
                ['name' => $p['name']],
                [
                    'price'       => $p['price'],
                    'tax'         => $p['tax'],
                    'unit'        => $p['unit'],
                    'description' => $p['description'],
                ]
            );

            // Sync categories
            $catIds = collect($p['categories'])->map(fn($c) => $categories[$c]->id)->toArray();
            $product->categories()->sync($catIds);

            // Variants
            $product->variants()->delete();
            foreach ($p['variants'] as $v) {
                $product->variants()->create($v);
            }

            $productModels[$p['name']] = $product;
        }

        // ── Floors & Tables ────────────────────────────────────
        // Disable auto-seeding for existing floors
        $groundFloor = Floor::updateOrCreate(['name' => 'Ground Floor']);
        $firstFloor  = Floor::updateOrCreate(['name' => 'First Floor']);

        // Ensure each floor has tables
        if ($groundFloor->tables()->count() === 0) {
            for ($i = 1; $i <= 8; $i++) {
                $groundFloor->tables()->create([
                    'number' => (string)(100 + $i),
                    'seats'  => $i <= 4 ? 4 : ($i <= 6 ? 6 : 2),
                    'status' => 'vacant',
                ]);
            }
        }

        if ($firstFloor->tables()->count() === 0) {
            for ($i = 1; $i <= 6; $i++) {
                $firstFloor->tables()->create([
                    'number' => (string)(200 + $i),
                    'seats'  => $i <= 3 ? 4 : 6,
                    'status' => 'vacant',
                ]);
            }
        }

        // ── POS Session ────────────────────────────────────────
        $session = PosSession::where('status', 'open')->first();
        if (!$session) {
            $session = PosSession::create([
                'pos_config_id' => $config->id,
                'opened_by'     => $admin->id,
                'opened_at'     => now()->subHours(3),
                'status'        => 'open',
                'total_sales'   => 0,
            ]);
        }

        // ── Sample Orders ──────────────────────────────────────
        $tables = RestaurantTable::all();

        $sampleOrders = [
            [
                'table'   => $tables->where('number', '101')->first(),
                'status'  => 'paid',
                'payment' => 'cash',
                'items'   => [
                    ['product' => 'Cheese Burger',    'qty' => 2, 'variant' => null],
                    ['product' => 'Cold Coffee',       'qty' => 2, 'variant' => '250ml'],
                    ['product' => 'French Fries',      'qty' => 1, 'variant' => 'Medium'],
                ],
            ],
            [
                'table'   => $tables->where('number', '102')->first(),
                'status'  => 'paid',
                'payment' => 'upi',
                'items'   => [
                    ['product' => 'Margherita Pizza',  'qty' => 1, 'variant' => '8 inch'],
                    ['product' => 'Mango Lassi',        'qty' => 2, 'variant' => null],
                ],
            ],
            [
                'table'   => $tables->where('number', '103')->first(),
                'status'  => 'paid',
                'payment' => 'card',
                'items'   => [
                    ['product' => 'Grilled Chicken',   'qty' => 1, 'variant' => null],
                    ['product' => 'Truffle Pasta',     'qty' => 1, 'variant' => 'Full'],
                    ['product' => 'Chocolate Lava Cake','qty' => 2, 'variant' => null],
                    ['product' => 'Fresh Lime Soda',   'qty' => 2, 'variant' => null],
                ],
            ],
            [
                'table'   => $tables->where('number', '104')->first(),
                'status'  => 'preparing',
                'payment' => null,
                'items'   => [
                    ['product' => 'Paneer Butter Masala','qty' => 2, 'variant' => null],
                    ['product' => 'Water Bottle',       'qty' => 4, 'variant' => null],
                ],
            ],
            [
                'table'   => $tables->where('number', '105')->first(),
                'status'  => 'pending',
                'payment' => null,
                'items'   => [
                    ['product' => 'Gulab Jamun',        'qty' => 2, 'variant' => '4 pcs'],
                    ['product' => 'Cold Coffee',        'qty' => 1, 'variant' => '500ml'],
                ],
            ],
        ];

        foreach ($sampleOrders as $orderData) {
            if (!$orderData['table']) continue;

            $order = Order::create([
                'pos_session_id' => $session->id,
                'table_id'       => $orderData['table']->id,
                'status'         => $orderData['status'],
                'payment_method' => $orderData['payment'],
                'notes'          => null,
                'created_at'     => now()->subMinutes(rand(10, 180)),
            ]);

            foreach ($orderData['items'] as $itemData) {
                $product = $productModels[$itemData['product']] ?? null;
                if (!$product) continue;

                $variant   = null;
                $extraPrice = 0;
                if ($itemData['variant']) {
                    $variant = $product->variants()->where('name', $itemData['variant'])->first();
                    $extraPrice = $variant?->price ?? 0;
                }

                $price    = $product->price + $extraPrice;
                $qty      = $itemData['qty'];
                $subtotal = round($price * $qty, 2);
                $taxAmt   = round($subtotal * $product->tax / 100, 2);

                $order->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'name'       => $variant ? "{$product->name} ({$variant->name})" : $product->name,
                    'price'      => $price,
                    'quantity'   => $qty,
                    'subtotal'   => $subtotal,
                    'tax_rate'   => $product->tax,
                    'tax_amount' => $taxAmt,
                ]);
            }

            $order->load('items');
            $order->recalculate();

            if ($orderData['status'] === 'paid') {
                $orderData['table']->update(['status' => 'vacant']);
            } elseif (in_array($orderData['status'], ['pending', 'preparing'])) {
                $orderData['table']->update(['status' => 'occupied']);
            }
        }

        // Update session total_sales
        $totalSales = $session->orders()->where('status', 'paid')->sum('total');
        $session->update(['total_sales' => $totalSales]);

        $this->command->info('✅ Seeded: 3 users, 5 categories, 12 products, 2 floors, 14 tables, 1 session, 5 orders');
    }
}
