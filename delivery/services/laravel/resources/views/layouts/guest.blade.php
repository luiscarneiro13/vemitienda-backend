<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ChatPedido') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#242034] antialiased bg-[#faf9fc]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 sm:pt-0 bg-[#faf9fc]"
             style="background: radial-gradient(circle at 15% 15%, rgba(238, 232, 251, 0.9) 0%, transparent 45%), radial-gradient(circle at 85% 85%, rgba(16, 185, 129, 0.15) 0%, transparent 45%), #faf9fc;">
            <div>
                <a href="/" class="inline-flex items-center gap-2.5 font-display font-bold text-[28px] tracking-[-1px] text-[#242034] no-underline rounded-[12px] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6135bc] focus-visible:ring-offset-4">
                    <span class="w-10 h-10 grid place-items-center shrink-0">
                        <x-application-logo class="w-full h-full block" />
                    </span>
                    <span>Chat<span class="text-[#6135bc]">Pedido</span><span class="text-[#10b981]">.</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-7 sm:px-8 sm:py-8 bg-white border border-[#e3deec] shadow-[0_16px_40px_rgba(36,32,52,0.07)] overflow-hidden rounded-[20px]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
