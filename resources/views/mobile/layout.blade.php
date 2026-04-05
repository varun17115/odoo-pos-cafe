<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="{{ asset('assets/css/figtree.css') }}" rel="stylesheet"/>
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background:#111; min-height:100vh; }
        .mobile-wrap { max-width:430px; margin:0 auto; min-height:100vh; background:#1a1a2e; position:relative; }
        .sticky-bar { position:sticky; bottom:0; left:0; right:0; background:#2d1b4e; border-top:1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="font-sans antialiased text-white">
<div class="mobile-wrap">
    {{ $slot }}
</div>
</body>
</html>
