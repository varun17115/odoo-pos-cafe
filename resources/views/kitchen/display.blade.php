<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display — RestroFry</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white font-sans antialiased" style="height:100vh;overflow:hidden;">

<div x-data="kitchenDisplay()" x-init="init()" class="flex flex-col h-screen">

    {{-- TOP BAR --}}
    <div class="flex items-center justify-between bg-gray-900 border-b border-gray-800 px-5 h-14 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 bg-orange-500 rounded-md flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <span class="font-bold text-base">Kitchen Display</span>
            <span class="text-xs text-gray-500" x-text="currentTime"></span>
        </div>

        {{-- Filter tabs --}}
        <div class="flex items-center gap-1">
            <template x-for="tab in tabs" :key="tab.key">
                <button @click="activeFilter = tab.key"
                        :class="activeFilter === tab.key ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                        class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-medium transition">
                    <span x-text="tab.label"></span>
                    <span class="text-xs px-1.5 py-0.5 rounded-full"
                          :class="activeFilter === tab.key ? 'bg-white/20' : 'bg-gray-700'"
                          x-text="countByStatus(tab.key)"></span>
                </button>
            </template>
        </div>

        <div class="flex items-center gap-3">
            <input x-model="search" type="text" placeholder="Search order or product..."
                   class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 w-52">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-white transition text-xs">← Back</a>
        </div>
    </div>

    {{-- MAIN --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- LEFT SIDEBAR: product/category filter --}}
        <div class="w-44 bg-gray-900 border-r border-gray-800 flex-shrink-0 overflow-y-auto p-3 space-y-4">
            <button @click="clearFilters()"
                    class="w-full text-xs text-orange-400 hover:text-orange-300 border border-orange-900 hover:border-orange-700 px-3 py-1.5 rounded-lg transition">
                Clear Filter
            </button>

            <div>
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Product</p>
                <div class="space-y-0.5">
                    <template x-for="p in allProducts" :key="p">
                        <button @click="toggleProductFilter(p)"
                                :class="productFilter.includes(p) ? 'bg-orange-500/20 text-orange-400 border-orange-700' : 'text-gray-400 hover:text-white border-transparent'"
                                class="w-full text-left text-xs px-2 py-1.5 rounded-lg border transition truncate"
                                x-text="p"></button>
                    </template>
                </div>
            </div>

            <div>
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Category</p>
                <div class="space-y-0.5">
                    <template x-for="c in allCategories" :key="c">
                        <button @click="toggleCategoryFilter(c)"
                                :class="categoryFilter.includes(c) ? 'bg-orange-500/20 text-orange-400 border-orange-700' : 'text-gray-400 hover:text-white border-transparent'"
                                class="w-full text-left text-xs px-2 py-1.5 rounded-lg border transition truncate"
                                x-text="c"></button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ORDER CARDS --}}
        <div class="flex-1 overflow-y-auto p-4">
            <template x-if="filteredOrders.length === 0">
                <div class="flex items-center justify-center h-full text-gray-600 text-sm">
                    No orders in queue.
                </div>
            </template>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 items-start">
                <template x-for="order in filteredOrders" :key="order.id">
                    <div class="rounded-xl border flex flex-col overflow-hidden"
                         :class="{
                             'border-blue-500/50 bg-gray-900': order.status === 'preparing',
                             'border-green-500/50 bg-gray-900': order.status === 'ready',
                         }">

                        {{-- Card header --}}
                        <div class="flex items-center justify-between px-3 py-2 border-b"
                             :class="order.status === 'ready' ? 'border-green-800 bg-green-900/20' : 'border-gray-800 bg-gray-800/50'">
                            <div>
                                <span class="font-bold text-sm" x-text="'#' + order.id"></span>
                                <template x-if="order.table">
                                    <span class="text-gray-500 text-xs ml-1.5"
                                          x-text="'T-' + order.table.number"></span>
                                </template>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                  :class="order.status === 'ready' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400'"
                                  x-text="order.status === 'ready' ? 'Ready' : 'Preparing'"></span>
                        </div>

                        {{-- Timer --}}
                        <div class="px-3 pt-1.5 pb-0">
                            <span class="text-xs text-gray-500"
                                  x-text="elapsed(order.updated_at)"></span>
                        </div>

                        {{-- Items --}}
                        <div class="px-3 py-2 flex-1 space-y-1">
                            <template x-for="item in visibleItems(order)" :key="item.id">
                                <button @click="toggleItem(order, item)"
                                        class="w-full flex items-center gap-2 text-left group transition">
                                    <span class="w-5 h-5 rounded border flex-shrink-0 flex items-center justify-center transition"
                                          :class="item.done ? 'bg-green-500 border-green-500' : 'border-gray-600 group-hover:border-orange-400'">
                                        <svg x-show="item.done" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span class="text-sm transition"
                                          :class="item.done ? 'line-through text-gray-600' : 'text-white group-hover:text-orange-300'"
                                          x-text="item.quantity + ' × ' + item.name"></span>
                                </button>
                            </template>
                        </div>

                        {{-- Card footer actions --}}
                        <div class="px-3 pb-3 pt-2 border-t border-gray-800 flex gap-2">
                            <template x-if="order.status === 'preparing'">
                                <button @click="markReady(order)"
                                        class="flex-1 py-1.5 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition">
                                    Mark Ready ✓
                                </button>
                            </template>
                            <template x-if="order.status === 'ready'">
                                <button @click="markPreparing(order)"
                                        class="flex-1 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs font-medium rounded-lg transition">
                                    ← Back to Prep
                                </button>
                            </template>
                            <button @click="draftOrder(order)"
                                    class="py-1.5 px-2 bg-gray-800 hover:bg-red-900/40 text-gray-500 hover:text-red-400 text-xs rounded-lg border border-gray-700 hover:border-red-800 transition">
                                Draft
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const api  = (url, opts = {}) => fetch(url, {
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    ...opts,
}).then(r => r.json());

function kitchenDisplay() {
    return {
        orders: [],
        activeFilter: 'all',
        search: '',
        productFilter: [],
        categoryFilter: [],
        currentTime: '',
        pollInterval: null,

        tabs: [
            { key: 'all',       label: 'All' },
            { key: 'preparing', label: 'To Cook' },
            { key: 'ready',     label: 'Ready' },
        ],

        get allProducts() {
            const names = new Set();
            this.orders.forEach(o => o.items.forEach(i => names.add(i.name)));
            return [...names].sort();
        },

        get allCategories() {
            const names = new Set();
            this.orders.forEach(o =>
                o.items.forEach(i =>
                    (i.product?.categories || []).forEach(c => names.add(c.name))
                )
            );
            return [...names].sort();
        },

        get filteredOrders() {
            let list = this.orders;

            // Status filter
            if (this.activeFilter !== 'all') {
                list = list.filter(o => o.status === this.activeFilter);
            }

            // Search
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                list = list.filter(o =>
                    String(o.id).includes(q) ||
                    o.items.some(i => i.name.toLowerCase().includes(q))
                );
            }

            // Product filter
            if (this.productFilter.length) {
                list = list.filter(o =>
                    o.items.some(i => this.productFilter.includes(i.name))
                );
            }

            // Category filter
            if (this.categoryFilter.length) {
                list = list.filter(o =>
                    o.items.some(i =>
                        (i.product?.categories || []).some(c => this.categoryFilter.includes(c.name))
                    )
                );
            }

            return list;
        },

        countByStatus(key) {
            if (key === 'all') return this.orders.length;
            return this.orders.filter(o => o.status === key).length;
        },

        visibleItems(order) {
            let items = order.items;
            if (this.productFilter.length) {
                items = items.filter(i => this.productFilter.includes(i.name));
            }
            if (this.categoryFilter.length) {
                items = items.filter(i =>
                    (i.product?.categories || []).some(c => this.categoryFilter.includes(c.name))
                );
            }
            return items.length ? items : order.items; // fallback to all if filter hides everything
        },

        toggleProductFilter(name) {
            const i = this.productFilter.indexOf(name);
            if (i === -1) this.productFilter.push(name);
            else this.productFilter.splice(i, 1);
        },

        toggleCategoryFilter(name) {
            const i = this.categoryFilter.indexOf(name);
            if (i === -1) this.categoryFilter.push(name);
            else this.categoryFilter.splice(i, 1);
        },

        clearFilters() {
            this.productFilter = [];
            this.categoryFilter = [];
            this.search = '';
            this.activeFilter = 'all';
        },

        elapsed(updatedAt) {
            const diff = Math.floor((Date.now() - new Date(updatedAt)) / 1000);
            if (diff < 60) return diff + 's ago';
            const m = Math.floor(diff / 60);
            if (m < 60) return m + 'm ago';
            return Math.floor(m / 60) + 'h ' + (m % 60) + 'm ago';
        },

        async init() {
            await this.poll();
            this.pollInterval = setInterval(() => this.poll(), 10000);
            setInterval(() => {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }, 1000);
        },

        async poll() {
            try {
                const data = await api('/kitchen/orders');
                // Merge to preserve local done state until server confirms
                this.orders = data;
            } catch(e) { console.error(e); }
        },

        async toggleItem(order, item) {
            const result = await api(`/kitchen/orders/${order.id}/items/${item.id}/toggle`, { method: 'POST' });
            // Update in place
            const idx = this.orders.findIndex(o => o.id === order.id);
            if (idx !== -1) this.orders[idx] = result.order;
        },

        async markReady(order) {
            const updated = await api(`/kitchen/orders/${order.id}/ready`, { method: 'POST' });
            const idx = this.orders.findIndex(o => o.id === order.id);
            if (idx !== -1) this.orders[idx] = updated;
        },

        async markPreparing(order) {
            const updated = await api(`/kitchen/orders/${order.id}/preparing`, { method: 'POST' });
            const idx = this.orders.findIndex(o => o.id === order.id);
            if (idx !== -1) this.orders[idx] = updated;
        },

        async draftOrder(order) {
            if (!confirm(`Draft order #${order.id}? This will cancel it.`)) return;
            await api(`/pos/orders/${order.id}/draft`, { method: 'POST' });
            this.orders = this.orders.filter(o => o.id !== order.id);
        },
    };
}
</script>
</body>
</html>
