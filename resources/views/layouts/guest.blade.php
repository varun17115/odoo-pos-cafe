<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RestroFry') }} — Admin</title>
    <link href="{{ asset('assets/css/figtree.css') }}" rel="stylesheet" />
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
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
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto object-contain mx-auto mb-3" />
            <p class="text-gray-400 text-sm">Restaurant Management System</p>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md bg-gray-900/80 backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl p-8">
            {{ $slot }}
        </div>

        <p class="mt-6 text-gray-600 text-xs">&copy; {{ date('Y') }} RestroFry. All rights reserved.</p>
    </div>
</body>
</html>
