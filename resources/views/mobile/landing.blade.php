<x-mobile-layout title="Welcome">

    <div class="flex flex-col min-h-screen relative overflow-hidden">

        {{-- Background image --}}
        @if($config && $config->bg_image_1)
        <div class="absolute inset-0 z-0">
            <img src="{{ Storage::url($config->bg_image_1) }}" class="w-full h-full object-cover opacity-30" />
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-gray-950/60 to-gray-950"></div>
        </div>
        @else
        <div class="absolute inset-0 z-0 bg-gradient-to-b from-gray-900 to-gray-950"></div>
        @endif

        <div class="relative z-10 flex flex-col items-center justify-between min-h-screen p-8">

            {{-- Logo / Brand --}}
            <div class="text-center pt-12">
                <div class="w-20 h-20 bg-orange-500 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-2xl shadow-orange-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white">{{ config('app.name') }}</h1>
                @if($table)
                <p class="text-orange-400 mt-2 text-sm font-medium">Table {{ $table->number }}</p>
                @endif
            </div>

            {{-- Center content --}}
            <div class="text-center">
                <p class="text-gray-400 text-lg">Scan. Order. Enjoy.</p>
                <p class="text-gray-600 text-sm mt-2">Fresh food, delivered to your table</p>
            </div>

            {{-- CTA --}}
            <div class="w-full pb-8">
                <a href="{{ route('mobile.menu', $token) }}"
                   class="block w-full py-4 bg-orange-500 hover:bg-orange-400 text-white text-center text-lg font-bold rounded-2xl shadow-lg shadow-orange-500/30 transition">
                    Order Here
                </a>
                @php $orderIds = session()->get('mobile_orders_' . $token, []); @endphp
                @if(count($orderIds))
                <a href="{{ route('mobile.order.history', $token) }}"
                   class="block w-full py-3 mt-3 bg-white/10 text-white text-center text-sm font-medium rounded-2xl transition">
                    Track My Orders
                </a>
                @endif
            </div>
        </div>
    </div>

</x-mobile-layout>
