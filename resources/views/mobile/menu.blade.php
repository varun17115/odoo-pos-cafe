<x-mobile-layout title="Menu">

<div x-data="mobileMenu()" class="relative min-h-screen">

    {{-- ===== MENU VIEW ===== --}}
    <div x-show="!cartOpen" class="flex flex-col min-h-screen">

        {{-- Header --}}
        <div class="sticky top-0 z-20 bg-gray-900 border-b border-white/5 px-4 py-3">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('mobile.landing', $token) }}"
                   class="w-8 h-8 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input x-model="search" type="text" placeholder="Search product..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-500 focus:outline-none focus:border-orange-500" />
                </div>
            </div>

            {{-- Category tabs --}}
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                <button @click="activeCategory = null"
                        :class="activeCategory === null ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400'"
                        class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap flex-shrink-0 transition">
                    All
                </button>
                @foreach($categories as $cat)
                <button @click="activeCategory = {{ $cat->id }}"
                        :class="activeCategory === {{ $cat->id }} ? 'text-white' : 'text-gray-400'"
                        class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap flex-shrink-0 transition"
                        :style="activeCategory === {{ $cat->id }} ? 'background:{{ $cat->color }}' : 'background:{{ $cat->color }}33'">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Product grid --}}
        <div class="flex-1 p-4 pb-32">
            <div class="grid grid-cols-3 gap-3">
                @foreach($products as $product)
                <div class="relative"
                     x-show="isVisible({{ json_encode($product->categories->pluck('id')) }}, '{{ addslashes($product->name) }}')">
                    <a href="{{ route('mobile.product', [$token, $product->id]) }}"
                       class="block bg-gray-800 rounded-xl overflow-hidden border border-gray-700 hover:border-orange-500/50 transition">
                        <div class="aspect-square bg-gray-700 relative">
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover" />
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                            {{-- Qty badge --}}
                            <div x-show="getQty({{ $product->id }}) > 0"
                                 class="absolute top-1 right-1 w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                 x-text="getQty({{ $product->id }})"></div>
                        </div>
                        <div class="p-2">
                            <p class="text-white text-xs font-medium truncate">{{ $product->name }}</p>
                            <p class="text-orange-400 text-xs font-semibold mt-0.5">₹{{ number_format($product->price, 0) }}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Sticky cart bar --}}
        <div x-show="totalQty > 0" x-transition class="sticky-bar px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold" x-text="totalQty + ' QTY'"></p>
                    <p class="text-purple-300 text-xs" x-text="'Total: ₹' + totalPrice.toFixed(0) + (taxTotal > 0 ? ' (incl. tax)' : '')" ></p>
                </div>
                <button @click="cartOpen = true"
                        class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl border border-white/20">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- ===== CART / PAYMENT VIEW ===== --}}
    <div x-show="cartOpen" class="flex flex-col min-h-screen bg-gray-950">

        <div class="flex items-center gap-3 px-4 py-4 border-b border-white/5 bg-gray-900">
            <button @click="cartOpen = false"
                    class="w-8 h-8 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <h2 class="text-white font-semibold">Payment</h2>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3">
            <template x-if="cart.length === 0">
                <p class="text-gray-500 text-center py-12">Your cart is empty</p>
            </template>
            <template x-for="item in cart" :key="item.key">
                <div class="flex items-center gap-3 py-3 border-b border-white/5">
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium" x-text="item.name"></p>
                        <p class="text-orange-400 text-xs" x-text="'₹' + parseFloat(item.price).toFixed(0) + ' each'"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="decrement(item)"
                                class="w-7 h-7 bg-gray-800 rounded-full text-white text-sm flex items-center justify-center border border-gray-700">−</button>
                        <span class="text-white text-sm w-5 text-center font-semibold" x-text="item.qty"></span>
                        <button @click="increment(item)"
                                class="w-7 h-7 bg-orange-500 rounded-full text-white text-sm flex items-center justify-center">+</button>
                    </div>
                    <span class="text-orange-400 text-sm font-bold w-16 text-right"
                          x-text="'₹' + (item.price * item.qty).toFixed(0)"></span>
                </div>
            </template>
        </div>

        <div class="sticky-bar px-4 py-3">
            <div class="space-y-1 mb-3 px-1">
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Subtotal</span>
                    <span x-text="'₹' + subtotal.toFixed(0)"></span>
                </div>
                <div class="flex justify-between text-xs text-gray-400" x-show="taxTotal > 0">
                    <span>Tax</span>
                    <span x-text="'₹' + taxTotal.toFixed(0)"></span>
                </div>
                <div class="flex justify-between text-sm font-bold text-white pt-1 border-t border-white/10">
                    <span>Total</span>
                    <span class="text-orange-400" x-text="'₹' + totalPrice.toFixed(0)"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-white font-bold" x-text="'₹' + totalPrice.toFixed(0)"></p>
                <form method="POST" action="{{ route('mobile.order.place', $token) }}" id="order-form">
                    @csrf
                    <div id="order-items-input"></div>
                    <button type="button" @click="submitOrder()"
                            :disabled="cart.length === 0"
                            class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl border border-white/20 disabled:opacity-40">
                        Confirmed
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function mobileMenu() {
    const token = '{{ $token }}';
    return {
        activeCategory: null,
        search: '',
        cartOpen: false,
        orderNotes: '',
        cart: JSON.parse(localStorage.getItem('cart_' + token) || '[]'),

        get totalQty() { return this.cart.reduce((s, i) => s + i.qty, 0); },
        get subtotal() { return this.cart.reduce((s, i) => s + i.price * i.qty, 0); },
        get taxTotal() {
            return this.cart.reduce((s, i) => {
                const itemSub = i.price * i.qty;
                return s + Math.round(itemSub * (i.tax_rate || 0) / 100 * 100) / 100;
            }, 0);
        },
        get totalPrice() { return this.subtotal + this.taxTotal; },

        isVisible(categoryIds, name) {
            const matchesSearch = !this.search.trim() || name.toLowerCase().includes(this.search.toLowerCase());
            const matchesCategory = this.activeCategory === null || categoryIds.includes(this.activeCategory);
            return matchesSearch && matchesCategory;
        },

        getQty(productId) {
            return this.cart.filter(i => i.product_id === productId).reduce((s, i) => s + i.qty, 0);
        },

        increment(item) { item.qty++; this.saveCart(); },
        decrement(item) {
            item.qty--;
            if (item.qty <= 0) this.cart = this.cart.filter(i => i.key !== item.key);
            this.saveCart();
        },

        saveCart() { localStorage.setItem('cart_' + token, JSON.stringify(this.cart)); },

        submitOrder() {
            if (this.cart.length === 0) return;
            const container = document.getElementById('order-items-input');
            container.innerHTML = '';
            this.cart.forEach((item, idx) => {
                const fields = {
                    product_id: item.product_id,
                    name: item.name,
                    price: item.price,
                    quantity: item.qty,
                    variant_id: item.variant_id || '',
                };
                Object.entries(fields).forEach(([k, v]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${idx}][${k}]`;
                    input.value = v;
                    container.appendChild(input);
                });
            });
            localStorage.removeItem('cart_' + token);
            document.getElementById('order-form').submit();
        },
    }
}
</script>

</x-mobile-layout>
