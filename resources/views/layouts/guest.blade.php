<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RestroFry') }} — Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased" style="background-color:#0a0a0a;">

    {{-- Radial glow behind the card --}}
    <div class="fixed inset-0 pointer-events-none"
         style="background: radial-gradient(ellipse 80% 60% at 50% 40%, rgba(234,88,12,0.12) 0%, transparent 70%);">
    </div>

    <div class="relative min-h-screen flex flex-col items-center justify-center py-12 px-4">

        {{-- Logo / Brand --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-2xl shadow-2xl mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">RestroFry</h1>
            <p class="text-gray-400 text-sm mt-1">Restaurant Management System</p>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md bg-gray-900/80 backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl p-8">
            {{ $slot }}
        </div>

        <p class="mt-6 text-gray-600 text-xs">&copy; {{ date('Y') }} RestroFry. All rights reserved.</p>
    </div>
</body>
</html>
