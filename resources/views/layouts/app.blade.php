<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — RestroFry</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Dark-themed Swal preset used everywhere
    window.Swal = Swal.mixin({
        background: '#111827',
        color: '#f9fafb',
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#374151',
        buttonsStyling: true,
        customClass: {
            popup:         'swal-dark-popup',
            confirmButton: 'swal-confirm',
            cancelButton:  'swal-cancel',
        }
    });

    // Global delete confirm helper — call from onclick
    window.confirmDelete = function(form) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then(result => { if (result.isConfirmed) form.submit(); });
        return false;
    };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen" x-data="{ activeMenu: null, userMenu: false }">

    {{-- ===== TOP NAVBAR ===== --}}
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-0 flex items-center h-12 relative z-50">

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 mr-8 flex-shrink-0">
            <div class="w-6 h-6 bg-orange-500 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-sm">RestroFry</span>
        </a>

        {{-- Nav Items --}}
        <div class="flex items-center h-full">

            {{-- Orders --}}
            <div class="relative h-full flex items-center">
                <button @click="activeMenu = activeMenu === 'orders' ? null : 'orders'"
                        :class="activeMenu === 'orders' ? 'text-white border-b-2 border-orange-500' : 'text-gray-400 hover:text-white border-b-2 border-transparent'"
                        class="h-full px-4 text-sm font-medium transition flex items-center gap-1">
                    Orders
                    <svg class="w-3 h-3 transition-transform" :class="activeMenu === 'orders' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Products --}}
            <div class="relative h-full flex items-center">
                <button @click="activeMenu = activeMenu === 'products' ? null : 'products'"
                        :class="activeMenu === 'products' ? 'text-white border-b-2 border-orange-500' : 'text-gray-400 hover:text-white border-b-2 border-transparent'"
                        class="h-full px-4 text-sm font-medium transition flex items-center gap-1">
                    Products
                    <svg class="w-3 h-3 transition-transform" :class="activeMenu === 'products' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Reporting --}}
            <div class="relative h-full flex items-center">
                <button @click="activeMenu = activeMenu === 'reporting' ? null : 'reporting'"
                        :class="activeMenu === 'reporting' ? 'text-white border-b-2 border-orange-500' : 'text-gray-400 hover:text-white border-b-2 border-transparent'"
                        class="h-full px-4 text-sm font-medium transition flex items-center gap-1">
                    Reporting
                    <svg class="w-3 h-3 transition-transform" :class="activeMenu === 'reporting' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Floors --}}
            <div class="relative h-full flex items-center">
                <button @click="activeMenu = activeMenu === 'floors' ? null : 'floors'"
                        :class="activeMenu === 'floors' ? 'text-white border-b-2 border-orange-500' : 'text-gray-400 hover:text-white border-b-2 border-transparent'"
                        class="h-full px-4 text-sm font-medium transition flex items-center gap-1">
                    Floors
                    <svg class="w-3 h-3 transition-transform" :class="activeMenu === 'floors' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Right side: user --}}
        <div class="ml-auto flex items-center gap-3">
            <div class="relative" x-data>
                <button @click="userMenu = !userMenu"
                        class="flex items-center gap-2 text-gray-400 hover:text-white transition text-sm">
                    <div class="w-7 h-7 rounded-full bg-orange-500 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="userMenu" @click.outside="userMenu = false" x-transition
                     class="absolute right-0 top-full mt-2 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        POS Settings
                    </a>
                    <a href="{{ route('kitchen.display') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Kitchen Display
                    </a>
                        
                    <div class="border-t border-gray-700"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-gray-700 hover:text-red-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== MEGA DROPDOWN PANEL ===== --}}
    <div x-show="activeMenu !== null" @click.outside="activeMenu = null"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute left-0 right-0 z-40 bg-gray-900 border-b border-gray-800 shadow-2xl px-8 py-6">

        {{-- Orders Menu --}}
        <div x-show="activeMenu === 'orders'" class="flex gap-10">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Orders</p>
                <div class="space-y-1">
                    <a href="{{ route('pos.indexOrder') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Orders</a>
                    {{-- <a href="{{ route('pos.terminal') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">POS Terminal</a> --}}
                    
                    <a href="{{ route('pos.payments') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Payment</a>
                    <a href="{{ route('customers.index') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Customer</a>
                </div>
            </div>
        </div>

        {{-- Products Menu --}}
        <div x-show="activeMenu === 'products'" class="flex gap-10">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Products</p>
                <div class="space-y-1">
                    <a href="{{ route('products.index') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Products</a>
                    
                    <a href="{{ route('categories.index') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Category</a>
                </div>
            </div>
        </div>

        {{-- Reporting Menu --}}
        <div x-show="activeMenu === 'reporting'" class="flex gap-10">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Reporting</p>
                <div class="space-y-1">
                    <a href="#" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Dashboard</a>
                </div>
            </div>
        </div>

        {{-- Floors Menu --}}
        <div x-show="activeMenu === 'floors'" class="flex gap-10">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Floor Management</p>
                <div class="space-y-1">
                    <a href="{{ route('floors.index') }}" class="block px-3 py-1.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition">Manage Floors & Tables</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PAGE CONTENT ===== --}}
    <main class="relative z-30" @click="activeMenu = null">
        {{ $slot }}
    </main>

</body>
</html>
