<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Display — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0d0d0d; }
        .divider { width: 1px; background: rgba(255,255,255,0.08); }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .item-row { animation: fadeSlideIn 0.3s ease forwards; }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
        }
        .pulse-dot { animation: pulse-dot 1.5s ease-in-out infinite; }
    </style>
</head>
<body class="font-sans antialiased text-white overflow-hidden" style="height:100vh;">

<div x-data="customerDisplay()" x-init="init()" class="flex h-screen">

    {{-- ===== LEFT PANEL — fixed branding ===== --}}
    <div class="w-56 flex-shrink-0 flex flex-col justify-between p-6 border-r border-white/5">
        {{-- Logo / Brand --}}
        <div>
            <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-orange-500/20">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <h1 class="text-white font-bold text-lg leading-tight">{{ config('app.name') }}</h1>
            @if($config)
            <p class="text-gray-500 text-xs mt-1">{{ $config->name }}</p>
            @endif
        </div>

        {{-- Welcome message --}}
        <div>
            {{-- Restaurant illustration --}}
            <div class="mb-4">
                <svg viewBox="0 0 120 80" class="w-full opacity-30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Table -->
                    <rect x="20" y="52" width="80" height="6" rx="3" fill="#f97316"/>
                    <rect x="30" y="58" width="6" height="16" rx="2" fill="#f97316"/>
                    <rect x="84" y="58" width="6" height="16" rx="2" fill="#f97316"/>
                    <!-- Plate -->
                    <ellipse cx="60" cy="50" rx="22" ry="5" fill="#374151"/>
                    <ellipse cx="60" cy="49" rx="18" ry="4" fill="#4b5563"/>
                    <!-- Food on plate -->
                    <circle cx="55" cy="48" r="5" fill="#f97316" opacity="0.8"/>
                    <circle cx="65" cy="48" r="4" fill="#fb923c" opacity="0.8"/>
                    <circle cx="60" cy="45" r="3" fill="#fdba74" opacity="0.8"/>
                    <!-- Fork -->
                    <rect x="38" y="38" width="2" height="18" rx="1" fill="#6b7280"/>
                    <rect x="36" y="38" width="1" height="8" rx="0.5" fill="#6b7280"/>
                    <rect x="40" y="38" width="1" height="8" rx="0.5" fill="#6b7280"/>
                    <!-- Knife -->
                    <rect x="80" y="38" width="2" height="18" rx="1" fill="#6b7280"/>
                    <path d="M80 38 Q84 42 82 48" stroke="#6b7280" stroke-width="1.5" fill="none"/>
                    <!-- Steam -->
                    <path d="M52 40 Q50 36 52 32 Q54 28 52 24" stroke="#f97316" stroke-width="1.5" fill="none" opacity="0.5" stroke-linecap="round"/>
                    <path d="M60 38 Q58 34 60 30 Q62 26 60 22" stroke="#f97316" stroke-width="1.5" fill="none" opacity="0.5" stroke-linecap="round"/>
                    <path d="M68 40 Q66 36 68 32 Q70 28 68 24" stroke="#f97316" stroke-width="1.5" fill="none" opacity="0.5" stroke-linecap="round"/>
                    <!-- Wine glass -->
                    <path d="M96 30 Q100 38 98 44 L94 44 Q92 38 96 30Z" fill="#6b7280" opacity="0.6"/>
                    <rect x="95" y="44" width="2" height="8" rx="1" fill="#6b7280" opacity="0.6"/>
                    <rect x="92" y="52" width="8" height="2" rx="1" fill="#6b7280" opacity="0.6"/>
                    <!-- Candle -->
                    <rect x="14" y="40" width="5" height="12" rx="1" fill="#fbbf24" opacity="0.5"/>
                    <ellipse cx="16.5" cy="40" rx="2.5" ry="1" fill="#fde68a" opacity="0.5"/>
                    <path d="M16.5 37 Q17.5 35 16.5 33 Q15.5 35 16.5 37Z" fill="#f97316" opacity="0.8"/>
                </svg>
            </div>
            <p class="text-gray-400 text-sm leading-relaxed">Welcome to</p>
            <p class="text-white font-semibold text-base">'{{ config('app.name') }}'</p>
        </div>

        {{-- Footer --}}
        <div>
            <p class="text-gray-700 text-xs">Powered by RestoPOS</p>
        </div>
    </div>

    <div class="divider"></div>

    {{-- ===== RIGHT PANEL — dynamic content ===== --}}
    <div class="flex-1 flex items-center justify-center overflow-hidden relative">

        {{-- IDLE scene --}}
        <div x-show="scene === 'idle'" x-transition class="text-center px-8">
            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Ready to serve you</h2>
            <p class="text-gray-500">Your order will appear here</p>
            <div class="flex items-center justify-center gap-1.5 mt-6">
                <span class="w-2 h-2 rounded-full bg-orange-500 pulse-dot" style="animation-delay:0s"></span>
                <span class="w-2 h-2 rounded-full bg-orange-500 pulse-dot" style="animation-delay:0.3s"></span>
                <span class="w-2 h-2 rounded-full bg-orange-500 pulse-dot" style="animation-delay:0.6s"></span>
            </div>
        </div>

        {{-- ORDER scene --}}
        <div x-show="scene === 'order'" x-transition class="w-full h-full flex flex-col p-8 overflow-y-auto">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                <h2 class="text-lg font-semibold text-white">Your Order</h2>
                <template x-if="order && order.table">
                    <span class="text-gray-500 text-sm" x-text="'Table ' + order.table.number"></span>
                </template>
            </div>

            {{-- Items list --}}
            <div class="space-y-3 flex-1">
                <template x-for="(item, idx) in (order ? order.items : [])" :key="idx">
                    <div class="item-row flex items-center gap-4 py-3 border-b border-white/5"
                         :style="'animation-delay:' + (idx * 0.05) + 's'">
                        {{-- Product icon placeholder --}}
                        <div class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium" x-text="item.quantity + ' × ' + item.name"></p>
                            <p class="text-gray-500 text-xs" x-text="'₹' + parseFloat(item.price).toFixed(2) + ' each'"></p>
                        </div>
                        <p class="text-orange-400 font-semibold text-sm flex-shrink-0"
                           x-text="'₹' + (item.price * item.quantity).toFixed(2)"></p>
                    </div>
                </template>
            </div>

            {{-- Totals --}}
            <div class="mt-4 pt-4 border-t border-white/10 space-y-2">
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Sub Total</span>
                    <span x-text="'₹' + parseFloat(order ? order.subtotal : 0).toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Tax</span>
                    <span x-text="'₹' + parseFloat(order ? order.tax : 0).toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-lg font-bold text-white pt-2 border-t border-white/10">
                    <span>Total</span>
                    <span class="text-orange-400" x-text="'₹' + parseFloat(order ? order.total : 0).toFixed(2)"></span>
                </div>
            </div>

            {{-- Status badge --}}
            <div class="mt-4 flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold capitalize"
                      :class="{
                          'bg-yellow-500/20 text-yellow-400': order && order.status === 'pending',
                          'bg-blue-500/20 text-blue-400': order && order.status === 'preparing',
                          'bg-green-500/20 text-green-400': order && order.status === 'ready',
                          'bg-emerald-500/20 text-emerald-400': order && order.status === 'paid',
                      }"
                      x-text="order ? order.status : ''"></span>
            </div>
        </div>

        {{-- PAYMENT scene --}}
        <div x-show="scene === 'payment'" x-transition class="w-full h-full flex flex-col items-center justify-center p-8">
            <h2 class="text-2xl font-bold text-white mb-2">Payment</h2>
            <p class="text-gray-400 text-sm mb-8">Please complete your payment</p>

            <div class="text-center mb-8">
                <p class="text-gray-400 text-sm mb-1">Amount Due</p>
                <p class="text-5xl font-bold text-orange-400" x-text="'₹' + parseFloat(order ? order.total : 0).toFixed(2)"></p>
            </div>

            {{-- UPI QR --}}
            <template x-if="order && order.payment_method === 'upi'">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-40 h-40 bg-white rounded-2xl flex items-center justify-center p-3">
                        <svg class="w-full h-full text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h7v7H3V3zm1 1v5h5V4H4zm1 1h3v3H5V5zM14 3h7v7h-7V3zm1 1v5h5V4h-5zm1 1h3v3h-3V5zM3 14h7v7H3v-7zm1 1v5h5v-5H4zm1 1h3v3H5v-3zM14 14h2v2h-2v-2zm3 0h2v2h-2v-2zm-3 3h2v2h-2v-2zm3 0h2v2h-2v-2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-sm">Scan to pay via UPI</p>
                    @if($config && $config->upi_id)
                    <p class="text-white text-xs font-mono bg-gray-800 px-3 py-1.5 rounded-lg">{{ $config->upi_id }}</p>
                    @endif
                </div>
            </template>

            {{-- Cash/Card --}}
            <template x-if="!order || order.payment_method !== 'upi'">
                <div class="flex items-center gap-3 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span class="capitalize" x-text="order ? order.payment_method : 'Processing...'"></span>
                </div>
            </template>
        </div>

        {{-- THANK YOU scene --}}
        <div x-show="scene === 'thankyou'" x-transition class="text-center px-8">
            <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-green-500/30">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-3">Thank you!</h2>
            <p class="text-gray-400 text-lg">for shopping with us</p>
            <p class="text-gray-500 mt-2">See you again</p>
            <template x-if="order">
                <p class="text-orange-400 font-bold text-2xl mt-6" x-text="'₹' + parseFloat(order.total).toFixed(2) + ' paid'"></p>
            </template>
        </div>

    </div>
</div>

<script>
function customerDisplay() {
    return {
        scene: 'idle',
        order: null,
        lastUpdated: null,

        async init() {
            await this.poll();
            // Poll every 10 seconds
            setInterval(() => this.poll(), 10000);
        },

        async poll() {
            try {
                const resp = await fetch('/customer-display/state', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await resp.json();

                // Only update if state changed
                if (data.updated_at !== this.lastUpdated) {
                    this.lastUpdated = data.updated_at;
                    this.scene = data.scene || 'idle';
                    this.order = data.order || null;
                }
            } catch(e) {
                console.error('Poll error:', e);
            }
        }
    }
}
</script>
</body>
</html>
