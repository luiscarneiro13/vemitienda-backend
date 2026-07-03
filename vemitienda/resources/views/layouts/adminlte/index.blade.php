<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ve mi Tienda</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" sizes="192x192" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/favicon.png') }}" type="image/png">

    @include('layouts.adminlte.top')
</head>

<body class="flex min-h-screen" style="background:#f8f9fb; font-family:'Hanken Grotesk',sans-serif">

    @include('layouts.adminlte.sidebar')

    <div class="flex flex-col flex-1" style="margin-left:220px; min-height:100vh">

        @include('layouts.adminlte.navbar')

        <div id="app" class="flex-1 p-6">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

    </div>

    @include('layouts.adminlte.bottom')

    @toastr_js
    @yield('js')

</body>

</html>
