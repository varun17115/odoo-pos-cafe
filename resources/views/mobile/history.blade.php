<x-mobile-layout title="Order History">

<div class="flex flex-col min-h-screen" x-data="orderHistory()">

    {{-- Header --}}
    <div class="sticky top-0 z-20 bg-gray-900 border-b border-white/5 px-4 py-4 flex items-center gap-3">
        <a href="{{ route('mobile.landing', $token) }}"
           class="w-8 h-8 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-white font-semibold">Order History</h1>
    </div>

    <div class="flex-1 px-4 py-4 space-y-3 pb-24">
        @forelse($orders as $order)
        <div class="bg-gray-800 rounded-2xl p-4 border border-white/5"
             x-data="orderCard('{{ $order->status }}', {{ $order->id }}, '{{ $token }}', {{ $order->total }})">

            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-white font-bold">#{{ $order->id }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $order->created_at->format('d M, H:i') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                      :class="{
                          'bg-pink-500/20 text-pink-400': status === 'pending',
                          'bg-purple-500/20 text-purple-400': status === 'preparing',
                          'bg-green-500/20 text-green-400': status === 'ready',
                          'bg-emerald-500/20 text-emerald-400': status === 'paid',
                          'bg-red-500/20 text-red-400': status === 'cancelled',
                      }"
                      x-text="statusLabel(status)"></span>
            </div>

            {{-- Items --}}
            <div class="space-y-1 mb-3">
                @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">{{ $item->quantity }}× {{ $item->name }}</span>
                    <span class="text-gray-300">₹{{ number_format($item->subtotal, 0) }}</span>
                </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center pt-2 border-t border-white/5 mb-3">
                <span class="text-gray-500 text-xs">Total</span>
                <span class="text-orange-400 font-bold">₹{{ number_format($order->total, 0) }}</span>
            </div>

            {{-- Pay Now button when ready --}}
            <div x-show="status === 'ready'" x-transition>
                <button @click="paymentOpen = true"
                        class="w-full py-3 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-xl transition text-sm">
                    Pay Now — ₹{{ number_format($order->total, 0) }}
                </button>
            </div>

            {{-- Payment modal --}}
            <div x-show="paymentOpen" x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
                <div class="bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-sm p-6" @click.stop>

                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-white font-bold text-lg">Payment</h2>
                        <button @click="paymentOpen = false" class="text-gray-500 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="text-center mb-6">
                        <p class="text-gray-400 text-sm">Total Amount</p>
                        <p class="text-4xl font-bold text-white mt-1">₹{{ number_format($order->total, 0) }}</p>
                    </div>

                    {{-- Payment methods from config --}}
                    <div class="flex gap-3 mb-6">
                        @if($config && $config->payment_cash)
                        <button @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300'"
                                class="flex-1 py-3 border rounded-xl text-sm font-semibold transition flex flex-col items-center gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cash
                        </button>
                        @endif
                        @if($config && $config->payment_card)
                        <button @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300'"
                                class="flex-1 py-3 border rounded-xl text-sm font-semibold transition flex flex-col items-center gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Card
                        </button>
                        @endif
                        @if($config && $config->payment_upi)
                        <button @click="paymentMethod = 'upi'"
                                :class="paymentMethod === 'upi' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300'"
                                class="flex-1 py-3 border rounded-xl text-sm font-semibold transition flex flex-col items-center gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            UPI
                        </button>
                        @endif
                    </div>

                    {{-- UPI QR --}}
                    <div x-show="paymentMethod === 'upi'" class="flex flex-col items-center gap-2 mb-4">
                        <div class="w-28 h-28 bg-white rounded-xl flex items-center justify-center p-2">
                            <svg class="w-full h-full text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h7v7H3V3zm1 1v5h5V4H4zm1 1h3v3H5V5zM14 3h7v7h-7V3zm1 1v5h5V4h-5zm1 1h3v3h-3V5zM3 14h7v7H3v-7zm1 1v5h5v-5H4zm1 1h3v3H5v-3zM14 14h2v2h-2v-2zm3 0h2v2h-2v-2zm-3 3h2v2h-2v-2zm3 0h2v2h-2v-2z"/>
                            </svg>
                        </div>
                        @if($config && $config->upi_id)
                        <p class="text-gray-400 text-xs font-mono">{{ $config->upi_id }}</p>
                        @endif
                    </div>

                    <button @click="processPayment()"
                            :disabled="!paymentMethod || processing"
                            class="w-full py-3 bg-orange-500 hover:bg-orange-400 disabled:opacity-40 text-white font-bold rounded-xl transition">
                        <span x-show="!processing">Validate Payment</span>
                        <span x-show="processing">Processing...</span>
                    </button>
                </div>
            </div>

            {{-- Paid success --}}
            <div x-show="status === 'paid'" x-transition
                 class="flex items-center gap-2 text-emerald-400 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Payment completed
            </div>

        </div>
        @empty
        <div class="text-center py-16">
            <p class="text-gray-500">No orders yet</p>
            <a href="{{ route('mobile.menu', $token) }}" class="text-orange-400 text-sm mt-2 block">Start ordering</a>
        </div>
        @endforelse
    </div>

    <div class="px-4 pb-8">
        <a href="{{ route('mobile.landing', $token) }}"
           class="block w-full py-3 bg-gray-800 border border-white/10 text-white text-center font-medium rounded-2xl">
            Back
        </a>
    </div>

</div>

<script>
function orderHistory() {
    return {}
}

function orderCard(initialStatus, orderId, token, total) {
    return {
        status: initialStatus,
        paymentOpen: false,
        paymentMethod: null,
        processing: false,

        statusLabel(s) {
            const map = { pending: 'To Cook', preparing: 'Preparing', ready: 'Ready ✓', paid: 'Completed', cancelled: 'Cancelled' };
            return map[s] || s;
        },

        init() {
            if (['paid', 'cancelled'].includes(this.status)) return;
            const interval = setInterval(async () => {
                try {
                    const resp = await fetch(`/s/${token}/order/${orderId}/status`);
                    const data = await resp.json();
                    this.status = data.status;
                    if (['paid', 'cancelled'].includes(data.status)) clearInterval(interval);
                } catch(e) {}
            }, 8000);
        },

        async processPayment() {
            if (!this.paymentMethod) return;
            this.processing = true;
            try {
                const resp = await fetch(`/s/${token}/order/${orderId}/pay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ payment_method: this.paymentMethod }),
                });
                const data = await resp.json();
                if (data.ok) {
                    this.status = 'paid';
                    this.paymentOpen = false;
                }
            } catch(e) { console.error(e); }
            this.processing = false;
        },
    }
}
</script>

</x-mobile-layout>
