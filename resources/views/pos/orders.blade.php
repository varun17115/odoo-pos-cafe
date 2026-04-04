<x-app-layout>
    <x-slot name="title">Orders</x-slot>

    <div class="min-h-screen p-6" x-data="ordersPage()" x-init="init()">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-white font-bold text-xl">Orders</h1>
                @if($session)
                    <p class="text-gray-500 text-sm mt-0.5">Session #{{ $session->id }} — opened {{ $session->opened_at->format('d M Y, H:i') }}</p>
                @else
                    <p class="text-gray-500 text-sm mt-0.5">No active session</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                {{-- Bulk action bar --}}
                <template x-if="selected.length > 0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 bg-gray-800 px-3 py-1.5 rounded-lg">
                            <span x-text="selected.length"></span> Selected
                        </span>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-1 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition">
                                ★ Action
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false"
                                 class="absolute right-0 mt-1 w-36 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-10 overflow-hidden">
                                <button @click="bulkDraft(); open = false"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    Draft
                                </button>
                                <button @click="bulkDelete(); open = false"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <a href="{{ route('pos.terminal') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-lg transition">
                    Open Terminal
                </a>
            </div>
        </div>

        {{-- Status filter tabs --}}
        <div class="flex gap-2 mb-4 overflow-x-auto pb-1">
            @foreach(['all','pending','preparing','ready','paid','draft'] as $s)
            <button @click="filter = '{{ $s }}'; selected = []"
                    :class="filter === '{{ $s }}' ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition capitalize whitespace-nowrap flex-shrink-0">
                {{ $s === 'all' ? 'All' : ucfirst($s) }}
            </button>
            @endforeach
            <button @click="window.location.reload()"
                    class="text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-3 py-1 rounded-lg transition ml-auto">
                Refresh
            </button>
        </div>

        {{-- Orders table --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" @change="toggleAll($event)"
                                   class="rounded bg-gray-700 border-gray-600 text-orange-500 focus:ring-orange-500">
                        </th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Order #</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Session</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Date</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Table</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Customer</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Items</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Total</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Payment</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Status</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php $isDraft = $order->status === 'cancelled'; @endphp
                    <tr class="border-b border-gray-800 hover:bg-gray-800/40 transition"
                        x-show="filter === 'all' || (filter === 'draft' && '{{ $order->status }}' === 'cancelled') || filter === '{{ $order->status }}'">
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="{{ $order->id }}" x-model="selected"
                                   class="rounded bg-gray-700 border-gray-600 text-orange-500 focus:ring-orange-500">
                        </td>
                        <td class="px-4 py-3 text-white font-semibold">#{{ $order->id }}</td>
                        <td class="px-4 py-3 text-gray-400">Session #{{ $order->pos_session_id }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 text-gray-300">
                            @if($order->table)
                                {{ $order->table->floor->name ?? '' }} · T-{{ $order->table->number }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-300">{{ $order->customer_name ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $order->items->count() }}</td>
                        <td class="px-4 py-3 text-orange-400 font-semibold">₹{{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3 text-gray-400 capitalize">{{ $order->payment_method ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($isDraft)
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-700 text-gray-400">Draft</span>
                            @elseif($order->status === 'pending')
                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400">Pending</span>
                            @elseif($order->status === 'preparing')
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400">Preparing</span>
                            @elseif($order->status === 'ready')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400">Ready</span>
                            @elseif($order->status === 'paid')
                                <span class="px-2 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-400">Paid</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <button @click="viewOrder({{ $order->id }})"
                                        class="text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-2 py-1 rounded-lg transition">
                                    View
                                </button>
                                @if(!$isDraft && $order->status !== 'paid')
                                <button @click="draftOrder({{ $order->id }})"
                                        class="text-xs text-gray-500 hover:text-gray-300 border border-gray-700 hover:border-gray-500 px-2 py-1 rounded-lg transition">
                                    Draft
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center text-gray-500">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== ORDER DETAIL MODAL ===== --}}
        <div x-show="detailOpen" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-2xl" @click.stop>

                <template x-if="detailOrder">
                    <div>
                        {{-- Modal header --}}
                        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-800">
                            <h2 class="text-white font-bold text-lg" x-text="'Order #' + detailOrder.id"></h2>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold border"
                                      :class="{
                                          'bg-gray-700 text-gray-300 border-gray-600': detailOrder.status === 'cancelled',
                                          'bg-yellow-500/20 text-yellow-400 border-yellow-700': detailOrder.status === 'pending',
                                          'bg-blue-500/20 text-blue-400 border-blue-700': detailOrder.status === 'preparing',
                                          'bg-green-500/20 text-green-400 border-green-700': detailOrder.status === 'ready',
                                          'bg-emerald-500/20 text-emerald-400 border-emerald-700': detailOrder.status === 'paid',
                                      }"
                                      x-text="detailOrder.status === 'cancelled' ? 'Draft' : detailOrder.status.charAt(0).toUpperCase() + detailOrder.status.slice(1)">
                                </span>
                                <button @click="detailOpen = false" class="text-gray-500 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Order meta --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-6 py-4 border-b border-gray-800 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Order number</p>
                                <p class="text-white font-medium" x-text="'#' + detailOrder.id"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Session</p>
                                <p class="text-white font-medium" x-text="'#' + detailOrder.pos_session_id"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Date</p>
                                <p class="text-white font-medium" x-text="new Date(detailOrder.created_at).toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'})"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Table</p>
                                <p class="text-white font-medium" x-text="detailOrder.table ? (detailOrder.table.floor ? detailOrder.table.floor.name + ' · ' : '') + 'T-' + detailOrder.table.number : '—'"></p>
                            </div>
                            <div class="col-span-2 sm:col-span-4">
                                <p class="text-gray-500 text-xs mb-0.5">Customer</p>
                                <p class="text-white font-medium" x-text="detailOrder.customer_name || '—'"></p>
                            </div>
                        </div>

                        {{-- Items table --}}
                        <div class="px-6 py-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-700">
                                        <th class="text-left py-2 text-gray-500 font-medium">Product</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">QTY</th>
                                        <th class="text-right py-2 text-gray-500 font-medium">Amount</th>
                                        <th class="text-right py-2 text-gray-500 font-medium">Tax</th>
                                        <th class="text-right py-2 text-gray-500 font-medium">Sub-Total</th>
                                        <th class="text-right py-2 text-gray-500 font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in detailOrder.items" :key="item.id">
                                        <tr class="border-b border-gray-800">
                                            <td class="py-2.5 text-blue-400" x-text="item.name"></td>
                                            <td class="py-2.5 text-center text-gray-300" x-text="item.quantity"></td>
                                            <td class="py-2.5 text-right text-gray-300" x-text="'₹' + parseFloat(item.price).toFixed(2)"></td>
                                            <td class="py-2.5 text-right text-gray-400" x-text="item.tax_rate > 0 ? item.tax_rate + '%' : '—'"></td>
                                            <td class="py-2.5 text-right text-gray-300" x-text="'₹' + parseFloat(item.subtotal).toFixed(2)"></td>
                                            <td class="py-2.5 text-right text-orange-400 font-medium"
                                                x-text="'₹' + (parseFloat(item.subtotal) + parseFloat(item.tax_amount || 0)).toFixed(2)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            {{-- Totals --}}
                            <div class="mt-4 space-y-1 text-sm border-t border-gray-700 pt-3">
                                <div class="flex justify-between text-gray-400">
                                    <span>Total w/t</span>
                                    <span x-text="'₹' + parseFloat(detailOrder.subtotal).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-gray-400">
                                    <span>Tax</span>
                                    <span x-text="'₹' + parseFloat(detailOrder.tax).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between font-bold text-white text-base pt-1 border-t border-gray-700">
                                    <span>Final Total</span>
                                    <span class="text-orange-400" x-text="'₹' + parseFloat(detailOrder.total).toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Modal footer actions --}}
                        <div class="flex items-center justify-between px-6 pb-5 pt-2 border-t border-gray-800">
                            <div class="text-xs text-gray-500" x-text="detailOrder.notes ? 'Notes: ' + detailOrder.notes : ''"></div>
                            <div class="flex gap-2">
                                <template x-if="detailOrder.status !== 'cancelled' && detailOrder.status !== 'paid'">
                                    <button @click="draftOrder(detailOrder.id); detailOpen = false"
                                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-xl transition">
                                        Mark as Draft
                                    </button>
                                </template>
                                <button @click="detailOpen = false"
                                        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-400 text-sm rounded-xl transition">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!detailOrder">
                    <div class="p-12 text-center text-gray-500">Loading...</div>
                </template>
            </div>
        </div>

    </div>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const api  = (url, opts = {}) => fetch(url, {
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        ...opts,
    }).then(r => r.json());

    function ordersPage() {
        return {
            filter: 'all',
            selected: [],
            detailOpen: false,
            detailOrder: null,

            init() {},

            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = @json($orders->pluck('id'));
                } else {
                    this.selected = [];
                }
            },

            async viewOrder(id) {
                this.detailOrder = null;
                this.detailOpen = true;
                this.detailOrder = await api(`/pos/orders/${id}`);
            },

            async draftOrder(id) {
                await api(`/pos/orders/${id}/draft`, { method: 'POST' });
                window.location.reload();
            },

            async bulkDraft() {
                if (!this.selected.length) return;
                await api('/pos/orders/bulk-draft', {
                    method: 'POST',
                    body: JSON.stringify({ ids: this.selected }),
                });
                window.location.reload();
            },

            async bulkDelete() {
                if (!this.selected.length) return;
                const confirm = await Swal.fire({
                    title: 'Delete selected orders?',
                    text: 'Only draft orders will be deleted. This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                });
                if (!confirm.isConfirmed) return;
                await api('/pos/orders/bulk-delete', {
                    method: 'POST',
                    body: JSON.stringify({ ids: this.selected }),
                });
                window.location.reload();
            },
        };
    }
    </script>
</x-app-layout>
