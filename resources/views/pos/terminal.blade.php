<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Terminal — RestroFry</title>
    <link href="{{ asset('assets/css/figtree.css') }}" rel="stylesheet"/>
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        window.Swal = Swal.mixin({
            background: '#111827', color: '#f9fafb',
            confirmButtonColor: '#f97316', cancelButtonColor: '#374151',
        });
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const api = (url, opts = {}) => fetch(url, {
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            ...opts,
        }).then(r => r.json());
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white font-sans antialiased" style="height:100vh;overflow:hidden;background-color:#030712!important;color:#fff!important;">

<div x-data="posTerminal()" x-init="init()" class="flex flex-col h-screen">

    {{-- TOP BAR --}}
    <div class="flex items-center justify-between bg-gray-900 border-b border-gray-800 px-4 h-12 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-white font-semibold text-sm">{{ $posConfig->name }}</span>
            <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full">Session Open</span>
        </div>
        {{-- Tabs --}}
        <div class="flex items-center gap-1">
            <button @click="tab='table'" :class="tab==='table' ? 'bg-orange-500 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition">Table</button>
            <button @click="tab='register'" :class="tab==='register' ? 'bg-orange-500 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition">Register</button>
            <button @click="tab='orders'; loadOrders(); newOrdersCount = 0"
                    :class="tab==='orders' ? 'bg-orange-500 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition relative">
                Orders
                <span x-show="newOrdersCount > 0"
                      class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-bold"
                      x-text="newOrdersCount > 9 ? '9+' : newOrdersCount"></span>
            </button>
        </div>
        <div class="flex items-center gap-2">
            {{-- Reload Data --}}
            <button @click="reloadData()" title="Reload Data"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-2.5 py-1 rounded-lg transition">
                <svg class="w-3.5 h-3.5" :class="reloading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reload
            </button>

            {{-- Go to Back-end --}}
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-2.5 py-1 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Back-end
            </a>

            @if(auth()->user()?->isAdmin())
            <span class="text-gray-600 text-xs">|</span>
            <span class="text-gray-500 text-xs">Session #{{ $session->id }}</span>
            <form method="POST" action="{{ route('pos.session.close', $session) }}">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:text-red-300 border border-red-800 hover:border-red-600 px-3 py-1 rounded-lg transition">
                    Close Session
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 overflow-hidden relative">

        {{-- ===== TABLE TAB ===== --}}
        <div x-show="tab==='table'" class="h-full flex flex-col">
            {{-- Floor selector --}}
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-900 border-b border-gray-800 overflow-x-auto flex-shrink-0">
                @foreach($floors as $floor)
                <button @click="selectFloor({{ $floor->id }})"
                        :class="activeFloorId === {{ $floor->id }} ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap flex-shrink-0">
                    {{ $floor->name }}
                </button>
                @endforeach
            </div>

            {{-- Table grid --}}
            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="floorLoading">
                    <div class="flex items-center justify-center h-32 text-gray-500">Loading tables...</div>
                </template>
                <template x-if="!floorLoading">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        <template x-for="table in floorTables" :key="table.id">
                            <button @click="selectTable(table)"
                                    class="bg-gray-900 border rounded-xl p-3 flex flex-col items-center gap-2 transition hover:scale-105"
                                    :class="{
                                        'border-green-500/50 hover:border-green-400': table.status === 'vacant',
                                        'border-red-500/50 hover:border-red-400': table.status === 'occupied',
                                        'border-yellow-500/50 hover:border-yellow-400': table.status === 'reserved',
                                        'border-gray-700 opacity-50 cursor-not-allowed': table.status === 'inactive'
                                    }"
                                    :disabled="table.status === 'inactive'">
                                {{-- SVG Table --}}
                                <div x-html="renderTableSvg(table)" class="w-16 h-16"></div>
                                <span class="text-xs font-semibold text-gray-300" x-text="'T-' + table.number"></span>
                                <span class="text-xs capitalize px-2 py-0.5 rounded-full"
                                      :class="{
                                          'bg-green-500/20 text-green-400': table.status === 'vacant',
                                          'bg-red-500/20 text-red-400': table.status === 'occupied',
                                          'bg-yellow-500/20 text-yellow-400': table.status === 'reserved',
                                          'bg-gray-700 text-gray-500': table.status === 'inactive'
                                      }"
                                      x-text="table.status"></span>
                            </button>
                        </template>
                        <template x-if="floorTables.length === 0">
                            <div class="col-span-full text-center text-gray-500 py-12">No tables on this floor.</div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===== REGISTER TAB ===== --}}
        <div x-show="tab==='register'" class="h-full flex overflow-hidden">

            {{-- LEFT: Product panel --}}
            <div class="flex-1 flex flex-col border-r border-gray-800 overflow-hidden">
                {{-- Search + category filter --}}
                <div class="p-3 border-b border-gray-800 flex-shrink-0">
                    <input x-model="productSearch" type="text" placeholder="Search products..."
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 mb-3">
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <button @click="activeCategoryId = null"
                                :class="activeCategoryId === null ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                                class="px-3 py-1 rounded-full text-xs font-medium transition whitespace-nowrap flex-shrink-0">All</button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="activeCategoryId = cat.id"
                                    :style="activeCategoryId === cat.id ? 'background:' + cat.color + ';color:#fff' : 'background:' + cat.color + '22;color:' + cat.color"
                                    class="px-3 py-1 rounded-full text-xs font-medium transition whitespace-nowrap flex-shrink-0"
                                    x-text="cat.name"></button>
                        </template>
                    </div>
                </div>

                {{-- Product grid --}}
                <div class="flex-1 overflow-y-auto p-3">
                    <template x-if="productsLoading">
                        <div class="text-center text-gray-500 py-12">Loading products...</div>
                    </template>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <button @click="addToOrder(product)"
                                    class="bg-gray-900 border border-gray-800 hover:border-orange-500/50 rounded-xl p-3 text-left transition hover:bg-gray-800 flex flex-col gap-2">
                                <div class="w-full aspect-square bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
                                    <template x-if="product.image">
                                        <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!product.image">
                                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </template>
                                </div>
                                <div>
                                    <p class="text-white text-xs font-medium leading-tight" x-text="product.name"></p>
                                    <p class="text-orange-400 text-xs font-semibold mt-0.5" x-text="'₹' + parseFloat(product.price).toFixed(2)"></p>
                                </div>
                            </button>
                        </template>
                        <template x-if="filteredProducts.length === 0 && !productsLoading">
                            <div class="col-span-full text-center text-gray-500 py-12">No products found.</div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Order bill --}}
            <div class="w-80 flex flex-col bg-gray-900 flex-shrink-0">
                {{-- Table info --}}
                <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between flex-shrink-0">
                    <div>
                        <p class="text-white font-semibold text-sm" x-text="selectedTable ? 'Table ' + selectedTable.number : 'No Table'"></p>
                        <p class="text-gray-500 text-xs" x-text="(currentOrder ? 'Order #' + currentOrder.id : 'New Order') + (customerName ? ' · ' + customerName : '')"></p>
                    </div>
                    <button @click="tab='table'" class="text-gray-500 hover:text-white transition text-xs">Change</button>
                </div>

                {{-- Order items --}}
                <div class="flex-1 overflow-y-auto px-3 py-2">
                    <template x-if="orderItems.length === 0">
                        <div class="text-center text-gray-600 py-8 text-sm">Add items to start an order</div>
                    </template>
                    <template x-for="(item, idx) in orderItems" :key="idx">
                        <div class="flex items-center gap-2 py-2 border-b border-gray-800">
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-xs font-medium truncate" x-text="item.name"></p>
                                <p class="text-gray-400 text-xs" x-text="'₹' + parseFloat(item.price).toFixed(2) + (item.tax_rate > 0 ? ' +' + item.tax_rate + '%' : '')"></p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="decrementItem(idx)" class="w-5 h-5 bg-gray-700 hover:bg-gray-600 rounded text-white text-xs flex items-center justify-center">−</button>
                                <span class="text-white text-xs w-5 text-center" x-text="item.quantity"></span>
                                <button @click="incrementItem(idx)" class="w-5 h-5 bg-gray-700 hover:bg-gray-600 rounded text-white text-xs flex items-center justify-center">+</button>
                            </div>
                            <span class="text-orange-400 text-xs font-semibold w-14 text-right" x-text="'₹' + (item.price * item.quantity).toFixed(2)"></span>
                            <button @click="removeItem(idx)" class="text-gray-600 hover:text-red-400 transition ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Notes --}}
                <div class="px-3 pb-2 flex-shrink-0">
                    <button @click="customerModalOpen = true"
                            class="w-full flex items-center justify-between bg-gray-800 border border-gray-700 hover:border-orange-500/50 rounded-lg px-3 py-2 text-xs mb-2 transition">
                        <span :class="customerName ? 'text-white' : 'text-gray-500'"
                              x-text="customerName || 'Select / add customer...'"></span>
                        <template x-if="customerId || customerName">
                            <button @click.stop="customerId = null; customerName = ''" class="text-gray-500 hover:text-red-400 transition ml-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </template>
                        <template x-if="!customerId && !customerName">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </template>
                    </button>
                    <textarea x-model="orderNotes" rows="2" placeholder="Notes..."
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 resize-none"></textarea>
                </div>

                {{-- Totals --}}
                <div class="px-4 py-3 border-t border-gray-800 flex-shrink-0 space-y-1">
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Subtotal</span>
                        <span x-text="'₹' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Tax (5%)</span>
                        <span x-text="'₹' + tax.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-white pt-1 border-t border-gray-700">
                        <span>Total</span>
                        <span x-text="'₹' + total.toFixed(2)"></span>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="px-3 pb-3 flex gap-2 flex-shrink-0">
                    <button @click="sendOrder()"
                            :disabled="orderItems.length === 0"
                            class="flex-1 py-2 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition">
                        Send
                    </button>
                    <button @click="openPayment()"
                            :disabled="orderItems.length === 0"
                            class="flex-1 py-2 bg-orange-500 hover:bg-orange-400 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition">
                        Pay ₹<span x-text="total.toFixed(2)"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== ORDERS TAB ===== --}}
        <div x-show="tab==='orders'" class="h-full flex flex-col overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-800 flex-shrink-0 overflow-x-auto">
                <template x-for="s in ['all','pending','preparing','ready','paid','draft']" :key="s">
                    <button @click="ordersFilter = s"
                            :class="ordersFilter === s ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition capitalize whitespace-nowrap flex-shrink-0"
                            x-text="s === 'all' ? 'All Orders' : s.charAt(0).toUpperCase() + s.slice(1)"></button>
                </template>
                <button @click="refreshOrders()" class="text-xs text-red-400 hover:text-red-300 border border-red-800 hover:border-red-600 px-3 py-1 rounded-lg transition">
                    Refresh Orders
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="sessionOrders.length === 0">
                    <div class="text-center text-gray-500 py-12">No orders yet.</div>
                </template>
                <div class="space-y-2">
                    <template x-for="order in filteredOrders" :key="order.id">
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-white font-semibold text-sm" x-text="'#' + order.id"></span>
                                    <span class="text-xs px-2 py-0.5 rounded-full capitalize"
                                          :class="{
                                              'bg-yellow-500/20 text-yellow-400': order.status === 'pending',
                                              'bg-blue-500/20 text-blue-400': order.status === 'preparing',
                                              'bg-green-500/20 text-green-400': order.status === 'ready' || order.status === 'paid',
                                              'bg-gray-700 text-gray-400': order.status === 'cancelled'
                                          }"
                                          x-text="order.status === 'cancelled' ? 'Draft' : order.status"></span>
                                    <template x-if="order.table">
                                        <span class="text-xs text-gray-400" x-text="(order.table.floor ? order.table.floor.name + ' · ' : '') + 'Table ' + order.table.number"></span>
                                    </template>
                                </div>
                                <p class="text-gray-500 text-xs">
                                    <span x-text="(order.items ? order.items.length : 0) + ' items'"></span>
                                    <template x-if="order.customer_name">
                                        <span x-text="' · ' + order.customer_name" class="text-gray-400"></span>
                                    </template>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-orange-400 font-semibold text-sm" x-text="'₹' + parseFloat(order.total).toFixed(2)"></p>
                                <p class="text-gray-600 text-xs" x-text="order.payment_method || '—'"></p>
                            </div>
                            <template x-if="order.status !== 'paid' && order.status !== 'cancelled'">
                                <div class="flex flex-col gap-1">
                                    <button @click="loadOrderIntoRegister(order)"
                                            class="text-xs text-orange-400 hover:text-orange-300 border border-orange-800 hover:border-orange-600 px-3 py-1.5 rounded-lg transition">
                                        Open
                                    </button>
                                    <button @click="draftOrder(order.id)"
                                            class="text-xs text-gray-500 hover:text-gray-300 border border-gray-700 hover:border-gray-500 px-3 py-1.5 rounded-lg transition">
                                        Draft
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>{{-- end main content --}}

    {{-- ===== NEW ORDER TOAST ===== --}}
    <div x-show="newOrderToast" x-transition
         class="fixed top-14 right-4 z-50 bg-orange-500 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-xs">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm">New Self-Order!</p>
            <p class="text-orange-100 text-xs">A customer placed an order from their table</p>
        </div>
        <button @click="newOrderToast = false; tab='orders'; loadOrders(); newOrdersCount = 0"
                class="text-orange-200 hover:text-white text-xs underline flex-shrink-0">View</button>
    </div>

    {{-- ===== VARIANT PICKER MODAL ===== --}}
    <div x-show="variantModalOpen" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-sm p-6" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-white font-bold text-base" x-text="variantProduct ? variantProduct.name : ''"></h2>
                    <p class="text-gray-500 text-xs mt-0.5">Select base product or a variant</p>
                </div>
                <button @click="closeVariantModal()" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-2 max-h-72 overflow-y-auto">
                {{-- Base product option --}}
                <button @click="addItemToOrder(variantProduct.id, null, variantProduct.name, parseFloat(variantProduct.price), parseFloat(variantProduct.tax || 0))"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-800 hover:bg-orange-500/20 hover:border-orange-500/50 border border-gray-700 rounded-xl transition">
                    <div class="text-left">
                        <p class="text-white text-sm font-medium" x-text="variantProduct ? variantProduct.name : ''"></p>
                        <p class="text-gray-500 text-xs">Base product</p>
                    </div>
                    <div class="text-right">
                        <p class="text-orange-400 font-semibold text-sm" x-text="variantProduct ? '₹' + parseFloat(variantProduct.price).toFixed(2) : ''"></p>
                        <p class="text-gray-600 text-xs" x-text="variantProduct && variantProduct.tax > 0 ? variantProduct.tax + '% tax' : 'No tax'"></p>
                    </div>
                </button>

                {{-- Variant options --}}
                <template x-if="variantProduct && variantProduct.variants.length > 0">
                    <div class="space-y-2">
                        <p class="text-gray-500 text-xs px-1 pt-1">Variants</p>
                        <template x-for="variant in variantProduct.variants" :key="variant.id">
                            <button @click="addVariantToOrder(variant)"
                                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-800 hover:bg-orange-500/20 hover:border-orange-500/50 border border-gray-700 rounded-xl transition">
                                <div class="text-left">
                                    <p class="text-white text-sm font-medium" x-text="variant.name"></p>
                                    <p class="text-gray-500 text-xs"
                                       x-text="variant.price > 0 ? 'Base ₹' + parseFloat(variantProduct.price).toFixed(2) + ' + ₹' + parseFloat(variant.price).toFixed(2) : 'Base price only'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-orange-400 font-semibold text-sm"
                                       x-text="'₹' + (parseFloat(variantProduct.price) + (variant.price ? parseFloat(variant.price) : 0)).toFixed(2)"></p>
                                    <p class="text-gray-600 text-xs" x-text="variantProduct.tax > 0 ? variantProduct.tax + '% tax' : 'No tax'"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <button @click="closeVariantModal()"
                    class="w-full mt-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-400 text-sm rounded-xl transition">
                Done
            </button>
        </div>
    </div>

    {{-- ===== PAYMENT MODAL ===== --}}
    <div x-show="paymentOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-md p-6" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-white font-bold text-lg">Payment</h2>
                <button @click="paymentOpen = false" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="text-center mb-6">
                <p class="text-gray-400 text-sm">Total Amount</p>
                <p class="text-4xl font-bold text-white mt-1">₹<span x-text="total.toFixed(2)"></span></p>
            </div>

            {{-- Payment method buttons --}}
            <div class="flex gap-3 mb-6">
                @if($posConfig->payment_cash)
                <button @click="paymentMethod = 'cash'"
                        :class="paymentMethod === 'cash' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300 hover:border-orange-500'"
                        class="flex-1 py-3 border rounded-xl text-sm font-semibold transition flex flex-col items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Cash
                </button>
                @endif
                @if($posConfig->payment_card)
                <button @click="paymentMethod = 'card'"
                        :class="paymentMethod === 'card' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300 hover:border-orange-500'"
                        class="flex-1 py-3 border rounded-xl text-sm font-semibold transition flex flex-col items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Card
                </button>
                @endif
                @if($posConfig->payment_upi)
                <button @click="paymentMethod = 'upi'"
                        :class="paymentMethod === 'upi' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300 hover:border-orange-500'"
                        class="flex-1 py-3 border rounded-xl text-sm font-semibold transition flex flex-col items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    UPI
                </button>
                @endif
            </div>

            {{-- Cash: tendered amount --}}
            <div x-show="paymentMethod === 'cash'" class="mb-4 space-y-3">
                <div>
                    <label class="text-gray-400 text-xs mb-1 block">Amount Tendered</label>
                    <input x-model.number="cashTendered" type="number" step="0.01" :min="total"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-orange-500">
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Change</span>
                    <span class="font-semibold" :class="cashTendered >= total ? 'text-green-400' : 'text-red-400'"
                          x-text="'₹' + Math.max(0, cashTendered - total).toFixed(2)"></span>
                </div>
            </div>

            {{-- UPI: QR placeholder --}}
            <div x-show="paymentMethod === 'upi'" class="mb-4 flex flex-col items-center gap-3">
                <div class="w-32 h-32 bg-white rounded-xl flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h7v7H3V3zm1 1v5h5V4H4zm1 1h3v3H5V5zM14 3h7v7h-7V3zm1 1v5h5V4h-5zm1 1h3v3h-3V5zM3 14h7v7H3v-7zm1 1v5h5v-5H4zm1 1h3v3H5v-3zM14 14h2v2h-2v-2zm3 0h2v2h-2v-2zm-3 3h2v2h-2v-2zm3 0h2v2h-2v-2z"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-xs">UPI ID: <span class="text-white">{{ $posConfig->upi_id ?? 'Not configured' }}</span></p>
            </div>

            {{-- Card: just info --}}
            <div x-show="paymentMethod === 'card'" class="mb-4 text-center text-gray-400 text-sm py-4">
                Present card to the terminal to complete payment.
            </div>

            <div class="flex items-center gap-3 mb-4">
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" x-model="isInvoice" class="rounded bg-gray-700 border-gray-600 text-orange-500">
                    Generate Invoice
                </label>
            </div>

            <button @click="processPayment()"
                    :disabled="!paymentMethod || paymentProcessing || (paymentMethod === 'cash' && cashTendered < total)"
                    class="w-full py-3 bg-orange-500 hover:bg-orange-400 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl transition">
                <span x-show="!paymentProcessing">Validate Payment</span>
                <span x-show="paymentProcessing">Processing...</span>
            </button>
        </div>
    </div>

    {{-- ===== PAYMENT SUCCESS OVERLAY ===== --}}
    <div x-show="paymentSuccess" x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-950/95">
        <div class="text-center">
            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-white text-3xl font-bold mb-2">Payment Successful</h2>
            <p class="text-gray-400 text-lg mb-1">Amount Paid</p>
            <p class="text-orange-400 text-4xl font-bold mb-8">₹<span x-text="paidOrder ? parseFloat(paidOrder.total).toFixed(2) : '0.00'"></span></p>
            <div class="flex gap-4 justify-center">
                <button class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-semibold transition">
                    Email Receipt
                </button>
                <button @click="continueAfterPayment()" class="px-6 py-3 bg-orange-500 hover:bg-orange-400 text-white rounded-xl font-semibold transition">
                    Continue
                </button>
            </div>
        </div>
    </div>
    {{-- ===== CUSTOMER PICKER MODAL ===== --}}
    <div x-show="customerModalOpen" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-md" @click.stop>

            <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-800">
                <h2 class="text-white font-bold text-base">Customer</h2>
                <button @click="customerModalOpen = false" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mode tabs --}}
            <div class="flex gap-1 px-5 pt-4">
                <button @click="customerMode = 'select'"
                        :class="customerMode === 'select' ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition">
                    Existing Customer
                </button>
                <button @click="customerMode = 'new'"
                        :class="customerMode === 'new' ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition">
                    New Customer
                </button>
            </div>

            {{-- SELECT mode --}}
            <div x-show="customerMode === 'select'" class="px-5 py-4 space-y-3">
                <input x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                       type="text" placeholder="Search by name, phone or email..."
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">

                <div class="max-h-56 overflow-y-auto space-y-1">
                    <template x-if="customerSearchLoading">
                        <div class="text-center text-gray-500 text-xs py-4">Searching...</div>
                    </template>
                    <template x-if="!customerSearchLoading && customerResults.length === 0 && customerSearch.length > 0">
                        <div class="text-center text-gray-500 text-xs py-4">No customers found.</div>
                    </template>
                    <template x-for="c in customerResults" :key="c.id">
                        <button @click="selectCustomer(c)"
                                class="w-full flex items-center justify-between px-3 py-2.5 bg-gray-800 hover:bg-orange-500/10 hover:border-orange-500/40 border border-gray-700 rounded-xl transition text-left">
                            <div>
                                <p class="text-white text-sm font-medium" x-text="c.name"></p>
                                <p class="text-gray-500 text-xs" x-text="[c.phone, c.email].filter(Boolean).join(' · ')"></p>
                            </div>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            {{-- NEW mode --}}
            <div x-show="customerMode === 'new'" class="px-5 py-4 space-y-3">
                <div>
                    <label class="text-gray-400 text-xs mb-1 block">Full Name *</label>
                    <input x-model="newCustomer.name" type="text" placeholder="e.g. Eric Smith"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-gray-400 text-xs mb-1 block">Email</label>
                        <input x-model="newCustomer.email" type="email" placeholder="email@example.com"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                    </div>
                    <div>
                        <label class="text-gray-400 text-xs mb-1 block">Phone</label>
                        <input x-model="newCustomer.phone" type="text" placeholder="+91 98989 89898"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-gray-400 text-xs mb-1 block">City</label>
                        <input x-model="newCustomer.city" type="text" placeholder="City"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                    </div>
                    <div>
                        <label class="text-gray-400 text-xs mb-1 block">State</label>
                        <input x-model="newCustomer.state" type="text" placeholder="State"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                    </div>
                </div>
                <button @click="createAndSelectCustomer()"
                        :disabled="!newCustomer.name.trim()"
                        class="w-full py-2.5 bg-orange-500 hover:bg-orange-400 disabled:opacity-40 text-white text-sm font-semibold rounded-xl transition">
                    Create & Select
                </button>
            </div>

        </div>
    </div>

</div>{{-- end x-data --}}

<script>
function posTerminal() {
    return {
        tab: 'table',
        // Floor / table
        activeFloorId: null,
        floorTables: [],
        floorLoading: false,
        selectedTable: null,
        // Products
        products: [],
        categories: [],
        productsLoading: false,
        productSearch: '',
        activeCategoryId: null,
        // Order
        currentOrder: null,
        orderItems: [],
        orderNotes: '',
        customerName: '',
        customerId: null,
        // Customer picker
        customerModalOpen: false,
        customerMode: 'select', // 'select' | 'new'
        customerSearch: '',
        customerResults: [],
        customerSearchLoading: false,
        newCustomer: { name: '', email: '', phone: '', city: '', state: '', country: 'India' },
        // Orders list
        sessionOrders: [],
        ordersFilter: 'all',
        // Payment
        paymentOpen: false,
        paymentMethod: null,
        cashTendered: 0,
        isInvoice: false,
        paymentProcessing: false,
        paymentSuccess: false,
        paidOrder: null,
        // Variant picker
        variantModalOpen: false,
        variantProduct: null,
        // New order notification
        newOrdersCount: 0,
        newOrderToast: false,
        lastOrderId: 0,
        reloading: false,

        get subtotal() {
            return this.orderItems.reduce((s, i) => s + i.price * i.quantity, 0);
        },
        get tax() {
            return this.orderItems.reduce((s, i) => {
                const itemSubtotal = i.price * i.quantity;
                return s + Math.round(itemSubtotal * (i.tax_rate || 0) / 100 * 100) / 100;
            }, 0);
        },
        get total() { return this.subtotal + this.tax; },

        get filteredProducts() {
            let list = this.products;
            if (this.activeCategoryId) {
                list = list.filter(p => p.categories.some(c => c.id === this.activeCategoryId));
            }
            if (this.productSearch.trim()) {
                const q = this.productSearch.toLowerCase();
                list = list.filter(p => p.name.toLowerCase().includes(q));
            }
            return list;
        },

        get filteredOrders() {
            if (this.ordersFilter === 'all') return this.sessionOrders;
            if (this.ordersFilter === 'draft') return this.sessionOrders.filter(o => o.status === 'cancelled');
            return this.sessionOrders.filter(o => o.status === this.ordersFilter);
        },

        async init() {
            // Load first floor
            const floors = @json($floors);
            if (floors.length > 0) {
                this.activeFloorId = floors[0].id;
                await this.loadFloor(floors[0].id);
            }
            await this.loadProducts();
            // Poll for new self-orders every 15 seconds
            setInterval(() => this.pollNewOrders(), 15000);
        },

        async loadFloor(floorId) {
            this.floorLoading = true;
            this.activeFloorId = floorId;
            try {
                const data = await api(`/pos/floor/${floorId}`);
                this.floorTables = data.tables || [];
            } catch(e) { console.error(e); }
            this.floorLoading = false;
        },

        async selectFloor(floorId) {
            await this.loadFloor(floorId);
        },

        async loadProducts() {
            this.productsLoading = true;
            try {
                const data = await api('/pos/products');
                this.products = data;
                // Extract unique categories
                const catMap = {};
                data.forEach(p => p.categories.forEach(c => { catMap[c.id] = c; }));
                this.categories = Object.values(catMap);
            } catch(e) { console.error(e); }
            this.productsLoading = false;
        },

        selectTable(table) {
            this.selectedTable = table;
            if (table.active_order) {
                this.loadExistingOrder(table.active_order);
            } else {
                this.currentOrder = null;
                this.orderItems = [];
                this.orderNotes = '';
            }
            this.tab = 'register';
        },

        async loadExistingOrder(orderId) {
            try {
                const order = await api(`/pos/orders/${orderId}`);
                this.currentOrder = order;
                this.customerId   = order.customer_id || null;
                this.customerName = order.customer_name || '';
                this.orderItems = (order.items || []).map(i => ({
                    id: i.id,
                    product_id: i.product_id,
                    variant_id: i.variant_id,
                    name: i.name,
                    price: parseFloat(i.price),
                    tax_rate: parseFloat(i.tax_rate || 0),
                    quantity: i.quantity,
                }));
                this.orderNotes = order.notes || '';
            } catch(e) { console.error(e); }
        },

        addToOrder(product) {
            // If product has variants, show picker modal
            if (product.variants && product.variants.length > 0) {
                this.variantProduct = product;
                this.variantModalOpen = true;
            } else {
                // No variants — add base product directly
                this.addItemToOrder(product.id, null, product.name, parseFloat(product.price), parseFloat(product.tax || 0));
            }
        },

        addItemToOrder(productId, variantId, name, price, taxRate) {
            const existing = this.orderItems.find(i => i.product_id === productId && i.variant_id === variantId);
            if (existing) {
                existing.quantity++;
            } else {
                this.orderItems.push({
                    product_id: productId,
                    variant_id: variantId,
                    name,
                    price,
                    tax_rate: taxRate,
                    quantity: 1,
                });
            }
            // Push live order to customer display
            this.pushToDisplay('order', {
                items: this.orderItems,
                subtotal: this.subtotal,
                tax: this.tax,
                total: this.total,
                status: 'pending',
                table: this.selectedTable,
            });
        },

        addVariantToOrder(variant) {
            const product = this.variantProduct;
            // Variant price is EXTRA on top of base price
            const extraPrice = variant.price ? parseFloat(variant.price) : 0;
            const finalPrice = parseFloat(product.price) + extraPrice;
            const name = `${product.name} (${variant.name})`;
            this.addItemToOrder(product.id, variant.id, name, finalPrice, parseFloat(product.tax || 0));
        },

        closeVariantModal() {
            this.variantModalOpen = false;
            this.variantProduct = null;
        },

        incrementItem(idx) { this.orderItems[idx].quantity++; },
        decrementItem(idx) {
            if (this.orderItems[idx].quantity > 1) this.orderItems[idx].quantity--;
            else this.removeItem(idx);
        },
        removeItem(idx) { this.orderItems.splice(idx, 1); },

        async sendOrder() {
            if (this.orderItems.length === 0) return;
            if (!this.selectedTable) {
                Swal.fire({ icon: 'error', title: 'Please Select a Table', timer: 1500, showConfirmButton: false });
                return;
            }
            try {
                const order = await this.saveOrder();
                if (order && order.id) {
                    await api(`/pos/orders/${order.id}/send`, {
                        method: 'POST',
                        body: JSON.stringify({ customer_name: this.customerName.trim() || null }),
                    });
                    this.currentOrder = { ...order, status: 'preparing' };
                    Swal.fire({ icon: 'success', title: 'Sent to Kitchen', timer: 1500, showConfirmButton: false });
                    this.pushToDisplay('order', this.currentOrder);
                }
            } catch(e) { console.error(e); }
        },

        async saveOrder() {
            const payload = {
                table_id:      this.selectedTable ? this.selectedTable.id : null,
                customer_id:   this.customerId || null,
                customer_name: this.customerName.trim() || null,
                notes:         this.orderNotes,
                items:         this.orderItems.map(i => ({
                    product_id: i.product_id,
                    variant_id: i.variant_id || null,
                    name:       i.name,
                    price:      i.price,
                    quantity:   i.quantity,
                })),
            };

            if (this.currentOrder && this.currentOrder.id) {
                const updated = await api(`/pos/orders/${this.currentOrder.id}/sync`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                this.currentOrder = updated;
                return updated;
            }

            const order = await api('/pos/orders', { method: 'POST', body: JSON.stringify(payload) });
            this.currentOrder = order;
            if (this.activeFloorId) await this.loadFloor(this.activeFloorId);
            return order;
        },

        openPayment() {
            this.paymentMethod = null;
            this.cashTendered = Math.ceil(this.total);
            this.isInvoice = false;
            this.paymentOpen = true;
            this.pushToDisplay('payment', this.currentOrder);
        },

        async processPayment() {
            if (!this.paymentMethod) return;
            this.paymentProcessing = true;
            try {
                let order = await this.saveOrder();
                if (!order || !order.id) throw new Error('No order');
                const resp = await fetch(`/pos/orders/${order.id}/pay`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ payment_method: this.paymentMethod }),
                });
                const paid = await resp.json();
                if (!resp.ok) {
                    Swal.fire({ icon: 'error', title: 'Cannot Process Payment', text: paid.error || 'Something went wrong.' });
                    this.paymentProcessing = false;
                    return;
                }
                this.paidOrder = paid;
                this.paymentOpen = false;
                this.paymentSuccess = true;
                this.pushToDisplay('thankyou', paid);
            } catch(e) {
                console.error(e);
                Swal.fire({ icon: 'error', title: 'Payment failed', text: 'Please try again.' });
            }
            this.paymentProcessing = false;
        },

        continueAfterPayment() {
            this.paymentSuccess = false;
            this.currentOrder = null;
            this.orderItems = [];
            this.orderNotes = '';
            this.customerName = '';
            this.customerId = null;
            this.selectedTable = null;
            this.paidOrder = null;
            this.tab = 'table';
            if (this.activeFloorId) this.loadFloor(this.activeFloorId);
            this.pushToDisplay('idle', null);
        },

        async pushToDisplay(scene, order) {
            try {
                await fetch('/customer-display/push', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ scene, order }),
                });
            } catch(e) { console.error('Display push error:', e); }
        },

        

        async searchCustomers() {
            if (!this.customerSearch.trim()) { this.customerResults = []; return; }
            this.customerSearchLoading = true;
            const resp = await fetch(`/customers/search?q=${encodeURIComponent(this.customerSearch)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            this.customerResults = await resp.json();
            this.customerSearchLoading = false;
        },

        selectCustomer(c) {
            this.customerId   = c.id;
            this.customerName = c.name;
            this.customerModalOpen = false;
            this.customerSearch = '';
            this.customerResults = [];
        },

        async createAndSelectCustomer() {
            if (!this.newCustomer.name.trim()) return;
            const resp = await api('/customers', { method: 'POST', body: JSON.stringify(this.newCustomer) });
            if (resp.id) {
                this.selectCustomer(resp);
                this.newCustomer = { name: '', email: '', phone: '', city: '', state: '', country: 'India' };
            }
        },

        async draftOrder(id) {
            await api(`/pos/orders/${id}/draft`, { method: 'POST' });
            // If this was the current open order, clear the register
            if (this.currentOrder && this.currentOrder.id === id) {
                this.currentOrder = null;
                this.orderItems = [];
                this.orderNotes = '';
                this.customerName = '';
                this.selectedTable = null;
            }
            await this.loadOrders();
            if (this.activeFloorId) await this.loadFloor(this.activeFloorId);
            Swal.fire({ icon: 'success', title: 'Order Drafted', timer: 1200, showConfirmButton: false });
        },

        async loadOrders() {
            try {
                const data = await api('/pos/orders');
                const resp = await fetch('/pos/orders', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                if (resp.headers.get('content-type')?.includes('application/json')) {
                    this.sessionOrders = await resp.json();
                } else {
                    // Fallback: load from current session orders already in memory
                }
            } catch(e) { console.error(e); }
        },
        
        async refreshOrders(){
            console.log("refreshing orders")
            this.loadOrders()
        },

        loadOrderIntoRegister(order) {
            this.currentOrder = order;
            this.customerId   = order.customer_id || null;
            this.customerName = order.customer_name || '';
            this.orderItems = (order.items || []).map(i => ({
                id: i.id,
                product_id: i.product_id,
                variant_id: i.variant_id,
                name: i.name,
                price: parseFloat(i.price),
                tax_rate: parseFloat(i.tax_rate || 0),
                quantity: i.quantity,
            }));
            this.orderNotes = order.notes || '';
            this.selectedTable = order.table || null;
            this.tab = 'register';
        },

        async reloadData() {
            this.reloading = true;
            await this.loadProducts();
            if (this.activeFloorId) await this.loadFloor(this.activeFloorId);
            await this.loadOrders();
            this.reloading = false;
            Swal.fire({ icon: 'success', title: 'Data Reloaded', timer: 1000, showConfirmButton: false });
        },

        async pollNewOrders() {
            try {
                const resp = await fetch('/pos/orders', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                if (!resp.headers.get('content-type')?.includes('application/json')) return;
                const orders = await resp.json();
                if (!orders.length) return;
                const latestId = Math.max(...orders.map(o => o.id));
                if (this.lastOrderId === 0) {
                    this.lastOrderId = latestId;
                    return;
                }
                // Count new orders since last check
                const newCount = orders.filter(o => o.id > this.lastOrderId).length;
                if (newCount > 0) {
                    this.newOrdersCount += newCount;
                    this.newOrderToast = true;
                    this.lastOrderId = latestId;
                    // Auto-hide toast after 6 seconds
                    setTimeout(() => { this.newOrderToast = false; }, 6000);
                }
            } catch(e) { console.error(e); }
        },

        renderTableSvg(table) {
            const seats = table.seats || 4;
            const statusColors = {
                vacant: '#22c55e', occupied: '#ef4444', reserved: '#f59e0b', inactive: '#6b7280'
            };
            const color = statusColors[table.status] || '#6b7280';
            const cx = 32, cy = 32, tableR = 16, chairR = 5, orbitR = 24;
            let chairs = '';
            for (let i = 0; i < seats; i++) {
                const angle = (2 * Math.PI * i / seats) - Math.PI / 2;
                const x = cx + orbitR * Math.cos(angle);
                const y = cy + orbitR * Math.sin(angle);
                chairs += `<circle cx="${x.toFixed(1)}" cy="${y.toFixed(1)}" r="${chairR}" fill="${color}" opacity="0.7"/>`;
            }
            return `<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                ${chairs}
                <circle cx="${cx}" cy="${cy}" r="${tableR}" fill="#1f2937" stroke="${color}" stroke-width="2"/>
                <text x="${cx}" y="${cy + 4}" text-anchor="middle" font-size="9" fill="${color}" font-family="sans-serif" font-weight="bold">${table.number}</text>
            </svg>`;
        },
    };
}
</script>
</body>
</html>
