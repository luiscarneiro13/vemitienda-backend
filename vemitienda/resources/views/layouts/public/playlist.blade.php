<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $playlist->name ?? 'Playlist' }} - Ve mi Tienda</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" sizes="192x192" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
    @include('layouts.partials.tailwind-config')

    <style>
        body {
            font-family: 'Hanken Grotesk', sans-serif;
            background-color: #f8f9fb;
        }
    </style>
</head>

<body class="min-h-screen">

    <div class="p-6">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @yield('js')

</body>

</html>
