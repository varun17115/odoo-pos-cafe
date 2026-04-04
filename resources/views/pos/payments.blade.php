<x-app-layout>
    <x-slot name="title">Payments</x-slot>

    <div class="min-h-screen p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-white font-bold text-xl">Payments</h1>
                {{-- <p class="text-gray-500 text-sm mt-0.5">All paid orders grouped by payment method</p> --}}
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl px-5 py-3 text-right">
                <p class="text-gray-500 text-xs mb-0.5">Grand Total</p>
                <p class="text-orange-400 font-bold text-xl">₹{{ number_format($grandTotal, 2) }}</p>
            </div>
        </div>

        <div class="space-y-4" x-data="paymentsPage()">

            @forelse($grouped as $method => $data)

            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">

                {{-- Group header --}}
                <button type="button"
                        @click="toggle('{{ $method }}')"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-800/50 transition">
                    <div class="flex items-center gap-3">
                        {{-- Method icon --}}
                        @if($method === 'cash')
                        <div class="w-8 h-8 rounded-lg bg-green-500/15 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        @elseif($method === 'card')
                        <div class="w-8 h-8 rounded-lg bg-blue-500/15 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        @elseif($method === 'upi')
                        <div class="w-8 h-8 rounded-lg bg-purple-500/15 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        @endif

                        <div class="text-left">
                            <p class="text-white font-semibold capitalize">{{ ucfirst($method) }}</p>
                            <p class="text-gray-500 text-xs">{{ $data['orders']->count() }} {{ Str::plural('order', $data['orders']->count()) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-orange-400 font-bold text-base">₹{{ number_format($data['total'], 2) }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                             :class="isOpen('{{ $method }}') ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                {{-- Orders table --}}
                <div x-show="isOpen('{{ $method }}')"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1">
                    <table class="w-full text-sm border-t border-gray-800">
                        <thead>
                            <tr class="bg-gray-800/40">
                                <th class="text-left px-5 py-2.5 text-gray-500 font-medium text-xs">Order #</th>
                                <th class="text-left px-5 py-2.5 text-gray-500 font-medium text-xs">Date</th>
                                <th class="text-left px-5 py-2.5 text-gray-500 font-medium text-xs">Customer</th>
                                <th class="text-left px-5 py-2.5 text-gray-500 font-medium text-xs">Table</th>
                                <th class="text-right px-5 py-2.5 text-gray-500 font-medium text-xs">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['orders'] as $order)
                            <tr class="border-t border-gray-800/60 hover:bg-gray-800/30 transition">
                                <td class="px-5 py-3 text-white font-medium">#{{ $order->id }}</td>
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-3 text-gray-300">{{ $order->customer_name ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-400">
                                    @if($order->table)
                                        {{ $order->table->floor->name ?? '' }} · T-{{ $order->table->number }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right text-orange-400 font-semibold">
                                    ₹{{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            @empty
            <div class="bg-gray-900 border border-gray-800 rounded-xl px-6 py-16 text-center text-gray-500">
                No paid orders found.
            </div>
            @endforelse

            {{-- Grand total bar --}}
            @if($grouped->isNotEmpty())
            <div class="flex items-center justify-between bg-gray-900 border border-gray-800 rounded-xl px-5 py-4">
                <span class="text-gray-400 font-medium text-sm">Grand Total</span>
                <span class="text-orange-400 font-bold text-lg">₹{{ number_format($grandTotal, 2) }}</span>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>

<script>
function paymentsPage() {
    return {
        expanded: @json($grouped->keys()->values()),
        toggle(method) {
            const i = this.expanded.indexOf(method);
            if (i === -1) this.expanded.push(method);
            else this.expanded.splice(i, 1);
        },
        isOpen(method) {
            return this.expanded.includes(method);
        },
    };
}
</script>
