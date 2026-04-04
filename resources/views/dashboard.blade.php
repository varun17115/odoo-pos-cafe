<x-app-layout>
    <x-slot name="title">POS Home</x-slot>

    <div class="min-h-screen p-6">

        <h2 class="text-gray-400 text-sm font-medium mb-4 uppercase tracking-wider">POS Terminals</h2>

        <div class="flex flex-wrap gap-4 mb-8">
            <div class="w-72 bg-gray-900 border border-gray-800 rounded-xl p-5 relative" x-data="{ terminalMenu: false }">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-white font-semibold text-base">{{ $activeConfig ? $activeConfig->name : config('app.name') }}</h3>
                        <div class="mt-2 space-y-0.5">
                            @if($openSession)
                                <p class="text-gray-500 text-xs">Session open since: <span class="text-green-400">{{ $openSession->opened_at->format('d M, H:i') }}</span></p>
                                <p class="text-gray-500 text-xs">Sales this session: <span class="text-gray-300">₹{{ number_format($openSession->total_sales, 2) }}</span></p>
                            @elseif($lastSession)
                                <p class="text-gray-500 text-xs">Last open: <span class="text-gray-400">{{ $lastSession->opened_at->format('d M, H:i') }}</span></p>
                                <p class="text-gray-500 text-xs">Last sell: <span class="text-gray-400">₹{{ number_format($lastSession->total_sales, 2) }}</span></p>
                            @else
                                <p class="text-gray-500 text-xs">Last open: <span class="text-gray-400">—</span></p>
                                <p class="text-gray-500 text-xs">Last sell: <span class="text-gray-400">—</span></p>
                            @endif
                        </div>
                    </div>
                    <div class="relative">
                        <button @click.stop="terminalMenu = !terminalMenu" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/></svg>
                        </button>
                        <div x-show="terminalMenu" @click.outside="terminalMenu = false" x-transition class="absolute right-0 top-full mt-1 w-44 bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden z-50">
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Setting
                            </a>
                            <a href="{{ route('kitchen.display') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Kitchen Display
                            </a>
                            <a href="{{ route('customer.display') }}" target="_blank" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Customer Display
                            </a>
                        </div>
                    </div>
                </div>
                @if($openSession)
                    <a href="{{ route('pos.terminal') }}" class="block w-full py-2 px-4 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-lg transition text-center">Continue Session</a>
                @elseif($activeConfig)
                    <form method="POST" action="{{ route('pos.session.open') }}">
                        @csrf
                        <input type="hidden" name="pos_config_id" value="{{ $activeConfig->id }}">
                        <button type="submit" class="w-full py-2 px-4 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-lg transition">Open Session</button>
                    </form>
                @else
                    <a href="{{ route('settings.index') }}" class="block w-full py-2 px-4 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-semibold rounded-lg transition text-center">Configure POS First</a>
                @endif
            </div>
        </div>

        {{-- ===== ANALYTICS ===== --}}
        <div class="border-t border-gray-800 pt-6">

            {{-- Header + period filter --}}
            <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                <h2 class="text-white font-semibold text-lg">Dashboard</h2>
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach(['today' => 'Today', 'week' => 'Weekly', 'month' => 'Monthly', 'year' => '365 Days'] as $key => $label)
                    <a href="{{ route('dashboard', ['period' => $key]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $period === $key ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                        {{ $label }}
                    </a>
                    @endforeach

                    <span class="text-gray-700">|</span>

                    {{-- Export CSV --}}
                    <a href="{{ route('dashboard.export', ['period' => $period, 'format' => 'csv']) }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-800 text-gray-400 hover:text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                @php
                    $kpis = [
                        ['label' => 'Total Orders',   'value' => $totalOrders,                    'prefix' => '',  'change' => $cntChange, 'color' => 'text-blue-400'],
                        ['label' => 'Revenue',         'value' => number_format($totalRevenue, 0), 'prefix' => '₹', 'change' => $revChange, 'color' => 'text-orange-400'],
                        ['label' => 'Average Order',   'value' => number_format($avgOrder, 0),     'prefix' => '₹', 'change' => $avgChange, 'color' => 'text-purple-400'],
                    ];
                @endphp
                @foreach($kpis as $kpi)
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <p class="text-gray-500 text-xs mb-2">{{ $kpi['label'] }}</p>
                    <p class="text-3xl font-bold {{ $kpi['color'] }}">{{ $kpi['prefix'] }}{{ $kpi['value'] }}</p>
                    <div class="flex items-center gap-1 mt-2">
                        @if($kpi['change'] >= 0)
                        <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        <span class="text-green-400 text-xs">+{{ $kpi['change'] }}%</span>
                        @else
                        <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        <span class="text-red-400 text-xs">{{ $kpi['change'] }}%</span>
                        @endif
                        <span class="text-gray-600 text-xs">since last period</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Sales Chart + Top Categories --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <h3 class="text-white font-semibold mb-4">Sales</h3>
                    @if($salesChart->isEmpty())
                    <div class="flex items-center justify-center h-32 text-gray-600 text-sm">No sales data for this period</div>
                    @else
                    @php
                        $maxVal = $salesChart->max() ?: 1;
                        $labels = $salesChart->keys()->toArray();
                        $values = $salesChart->values()->toArray();
                        $count  = count($values);
                    @endphp
                    <div class="relative">
                        <svg viewBox="0 0 400 120" class="w-full h-40" preserveAspectRatio="none">
                            @foreach([0,30,60,90,120] as $y)
                            <line x1="0" y1="{{ $y }}" x2="400" y2="{{ $y }}" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
                            @endforeach
                            @php
                                $pts = '';
                                foreach($values as $i => $v) {
                                    $x = $count > 1 ? ($i / ($count - 1)) * 400 : 200;
                                    $y = 115 - ($v / $maxVal) * 105;
                                    $pts .= round($x,1).','.round($y,1).' ';
                                }
                                $firstX = $count > 1 ? 0 : 200;
                                $lastX  = $count > 1 ? 400 : 200;
                            @endphp
                            <polygon points="{{ $firstX }},120 {{ trim($pts) }} {{ $lastX }},120" fill="rgba(249,115,22,0.12)"/>
                            <polyline points="{{ trim($pts) }}" fill="none" stroke="#f97316" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                            @foreach($values as $i => $v)
                            @php $x = $count > 1 ? ($i / ($count - 1)) * 400 : 200; $y = 115 - ($v / $maxVal) * 105; @endphp
                            <circle cx="{{ round($x,1) }}" cy="{{ round($y,1) }}" r="3" fill="#f97316"/>
                            @endforeach
                        </svg>
                        <div class="flex justify-between mt-1 overflow-hidden">
                            @foreach($labels as $label)
                            <span class="text-gray-600 text-xs truncate">{{ $label }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <h3 class="text-white font-semibold mb-4">Top Selling Category</h3>
                    @php $totalCatRev = $topCategories->sum('revenue') ?: 1; $catColors = ['#f97316','#3b82f6','#22c55e','#a855f7','#f59e0b']; @endphp
                    @if($topCategories->where('revenue', '>', 0)->isEmpty())
                    <div class="flex items-center justify-center h-32 text-gray-600 text-sm">No category data</div>
                    @else
                    <div class="space-y-3">
                        @foreach($topCategories->where('revenue', '>', 0) as $i => $cat)
                        @php $pct = round($cat->revenue / $totalCatRev * 100); @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $catColors[$i] ?? '#6b7280' }}"></span>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-300 truncate">{{ $cat->name }}</span>
                                    <span class="text-gray-500 ml-2">{{ $pct }}%</span>
                                </div>
                                <div class="h-1.5 bg-gray-800 rounded-full">
                                    <div class="h-1.5 rounded-full transition-all" style="width:{{ $pct }}%;background:{{ $catColors[$i] ?? '#6b7280' }}"></div>
                                </div>
                            </div>
                            <span class="text-gray-400 text-xs w-16 text-right flex-shrink-0">₹{{ number_format($cat->revenue, 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Top Orders --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between">
                    <h3 class="text-white font-semibold">Top Orders</h3>
                    <span class="text-gray-500 text-xs">Highest value orders</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="px-5 py-3 text-left text-xs text-gray-500 font-medium">Order</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 font-medium">Table</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 font-medium">Date</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 font-medium">Payment</th>
                            <th class="px-5 py-3 text-right text-xs text-gray-500 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($topOrders as $order)
                        <tr class="hover:bg-gray-800/40 transition">
                            <td class="px-5 py-3 text-orange-400 font-semibold">#{{ $order->id }}</td>
                            <td class="px-5 py-3 text-gray-400">{{ $order->table ? 'T-'.$order->table->number : '—' }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $order->created_at->format('d M, H:i') }}</td>
                            <td class="px-5 py-3 text-gray-400 capitalize">{{ $order->payment_method ?? '—' }}</td>
                            <td class="px-5 py-3 text-white font-semibold text-right">₹{{ number_format($order->total, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-600 text-sm">No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Top Products + Top Categories table --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-800">
                        <h3 class="text-blue-400 font-semibold text-sm">Top Product</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-gray-800">
                            <th class="px-5 py-2.5 text-left text-xs text-gray-500">Product</th>
                            <th class="px-5 py-2.5 text-left text-xs text-gray-500">Qty</th>
                            <th class="px-5 py-2.5 text-right text-xs text-gray-500">Revenue</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse($topProducts as $p)
                            <tr class="hover:bg-gray-800/40">
                                <td class="px-5 py-2.5 text-gray-300">{{ $p->name }}</td>
                                <td class="px-5 py-2.5 text-gray-400">{{ $p->qty }}</td>
                                <td class="px-5 py-2.5 text-orange-400 font-semibold text-right">₹{{ number_format($p->revenue, 0) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-gray-600 text-xs">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-800">
                        <h3 class="text-blue-400 font-semibold text-sm">Top Category</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-gray-800">
                            <th class="px-5 py-2.5 text-left text-xs text-gray-500">Category</th>
                            <th class="px-5 py-2.5 text-right text-xs text-gray-500">Revenue</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse($topCategories->where('revenue', '>', 0) as $cat)
                            <tr class="hover:bg-gray-800/40">
                                <td class="px-5 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $cat->color }}"></span>
                                        <span class="text-gray-300">{{ $cat->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-2.5 text-orange-400 font-semibold text-right">₹{{ number_format($cat->revenue, 0) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-5 py-6 text-center text-gray-600 text-xs">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
