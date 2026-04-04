<x-mobile-layout title="Order Confirmed">

<div class="flex flex-col items-center justify-center min-h-screen px-6 text-center">

    {{-- Success icon --}}
    <div class="w-24 h-24 rounded-full border-4 border-green-400 flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <p class="text-gray-400 text-sm mb-1">Order</p>
    <h1 class="text-white text-4xl font-bold mb-2">#{{ $order->id }}</h1>
    <p class="text-green-400 font-semibold text-lg mb-2">Order Confirmed</p>
    <p class="text-orange-400 text-3xl font-bold mb-8">₹{{ number_format($order->total, 0) }}</p>

    @if($table)
    <p class="text-gray-500 text-sm mb-8">Table {{ $table->number }} · Your order is being prepared</p>
    @endif

    <a href="{{ route('mobile.order.history', $token) }}"
       class="w-full py-4 bg-gray-800 border border-white/10 text-white text-center font-semibold rounded-2xl transition hover:bg-gray-700">
        Track My Order
    </a>

    <a href="{{ route('mobile.menu', $token) }}"
       class="w-full py-3 mt-3 text-gray-500 text-center text-sm transition hover:text-white">
        Order More
    </a>

</div>

</x-mobile-layout>
