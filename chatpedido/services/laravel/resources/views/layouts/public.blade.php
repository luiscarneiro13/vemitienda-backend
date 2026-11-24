<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>@yield('title', config('app.name'))</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/css/catalog.css', 'resources/js/app.js'])
        @if (isset($payload['settings']))
            <style>
                :root {
                    --cat-primary: {{ $payload['settings']['primary_color'] ?? '#a04100' }};
                    --cat-secondary: {{ $payload['settings']['secondary_color'] ?? '#b7131a' }};
                }
            </style>
        @endif
        @stack('styles')
    </head>
    <body class="font-sans antialiased {{ isset($payload['settings']) && ($payload['settings']['template_id'] ?? '') === 'dark_midnight' ? 'dark-mode bg-[#121212] text-[#f9fafb]' : 'bg-gray-50 text-gray-900' }}">
        @yield('content')
    </body>
</html>
