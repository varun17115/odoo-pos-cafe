<x-mobile-layout title="{{ $product->name }}">

<div class="flex flex-col min-h-screen" x-data="productPage()">

    {{-- Back header --}}
    <div class="sticky top-0 z-20 bg-gray-900 border-b border-white/5 px-4 py-3 flex items-center gap-3">
        <a href="{{ route('mobile.menu', $token) }}"
           class="w-8 h-8 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-white font-semibold text-sm">{{ $product->name }}</h1>
    </div>

    {{-- Product image --}}
    <div class="relative h-56 bg-gray-800">
        @if($product->image)
        <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover" />
        @else
        <div class="w-full h-full flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif
        {{-- Cart badge --}}
        <div x-show="cartQty > 0"
             class="absolute top-3 right-3 w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg"
             x-text="cartQty"></div>
    </div>

    {{-- Product info --}}
    <div class="flex-1 px-4 py-4 pb-32 space-y-5">

        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-white font-bold text-xl">{{ $product->name }}</h2>
                @if($product->description)
                <p class="text-gray-500 text-sm mt-1">{{ $product->description }}</p>
                @endif
            </div>
            <p class="text-orange-400 font-bold text-xl flex-shrink-0 ml-4">₹{{ number_format($product->price, 0) }}</p>
        </div>

        {{-- Variants (radio) --}}
        @if($product->variants->count())
        <div>
            <p class="text-white text-sm font-semibold mb-3">Choose Size / Variant</p>
            <div class="space-y-2">
                {{-- Base option --}}
                <label class="flex items-center justify-between p-3 bg-gray-800 rounded-xl border border-gray-700 cursor-pointer"
                       :class="selectedVariant === null ? 'border-orange-500' : 'border-gray-700'">
                    <div class="flex items-center gap-3">
                        <input type="radio" x-model="selectedVariantId" value="" @change="selectedVariant = null"
                               class="text-orange-500 focus:ring-orange-500 bg-gray-700 border-gray-600" />
                        <span class="text-white text-sm">{{ $product->name }} (Base)</span>
                    </div>
                    <span class="text-orange-400 text-sm font-semibold">₹{{ number_format($product->price, 0) }}</span>
                </label>
                @foreach($product->variants as $variant)
                @php $finalPrice = $product->price + ($variant->price ?? 0); @endphp
                <label class="flex items-center justify-between p-3 bg-gray-800 rounded-xl border cursor-pointer"
                       :class="selectedVariantId === '{{ $variant->id }}' ? 'border-orange-500' : 'border-gray-700'">
                    <div class="flex items-center gap-3">
                        <input type="radio" x-model="selectedVariantId" value="{{ $variant->id }}"
                               @change="selectedVariant = { id: {{ $variant->id }}, name: '{{ addslashes($variant->name) }}', extra: {{ $variant->price ?? 0 }} }"
                               class="text-orange-500 focus:ring-orange-500 bg-gray-700 border-gray-600" />
                        <div>
                            <span class="text-white text-sm">{{ $variant->name }}</span>
                            @if($variant->price)
                            <span class="text-orange-400 text-xs ml-1">+₹{{ number_format($variant->price, 0) }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-orange-400 text-sm font-semibold">₹{{ number_format($finalPrice, 0) }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Qty selector --}}
        <div class="flex items-center justify-between">
            <p class="text-white text-sm font-semibold">Quantity</p>
            <div class="flex items-center gap-4">
                <button @click="qty > 1 ? qty-- : null"
                        class="w-9 h-9 bg-gray-800 rounded-full text-white text-lg flex items-center justify-center border border-gray-700">−</button>
                <span class="text-white font-bold text-lg w-6 text-center" x-text="qty"></span>
                <button @click="qty++"
                        class="w-9 h-9 bg-orange-500 rounded-full text-white text-lg flex items-center justify-center">+</button>
            </div>
        </div>

    </div>

    {{-- Sticky bottom bar --}}
    <div class="sticky-bar px-4 py-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-xs" x-text="totalCartQty + ' QTY'"></p>
                <p class="text-purple-300 text-xs" x-text="'Total: ₹' + totalCartPrice.toFixed(0)"></p>
            </div>
            <button @click="addToCart()"
                    class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl border border-white/20">
                Next
            </button>
        </div>
    </div>

</div>

<script>
function productPage() {
    const token = '{{ $token }}';
    const baseProduct = {
        id: {{ $product->id }},
        name: '{{ addslashes($product->name) }}',
        price: {{ $product->price }},
        tax: {{ $product->tax ?? 0 }},
    };

    return {
        qty: 1,
        selectedVariantId: '',
        selectedVariant: null,
        cart: JSON.parse(localStorage.getItem('cart_' + token) || '[]'),

        get finalPrice() {
            return baseProduct.price + (this.selectedVariant ? this.selectedVariant.extra : 0);
        },
        get cartQty() {
            return this.cart.reduce((s, i) => s + i.qty, 0);
        },
        get totalCartQty() {
            return this.cart.reduce((s, i) => s + i.qty, 0) + this.qty;
        },
        get totalCartPrice() {
            return this.cart.reduce((s, i) => s + i.price * i.qty, 0) + this.finalPrice * this.qty;
        },

        addToCart() {
            const variantName = this.selectedVariant ? ` (${this.selectedVariant.name})` : '';
            const key = `${baseProduct.id}_${this.selectedVariantId || 'base'}`;
            const existing = this.cart.find(i => i.key === key);
            if (existing) {
                existing.qty += this.qty;
            } else {
                this.cart.push({
                    key,
                    product_id: baseProduct.id,
                    variant_id: this.selectedVariant ? this.selectedVariant.id : null,
                    name: baseProduct.name + variantName,
                    price: this.finalPrice,
                    tax_rate: baseProduct.tax,
                    qty: this.qty,
                });
            }
            localStorage.setItem('cart_' + token, JSON.stringify(this.cart));
            window.location.href = '{{ route('mobile.menu', $token) }}';
        },
    }
}
</script>

</x-mobile-layout>
