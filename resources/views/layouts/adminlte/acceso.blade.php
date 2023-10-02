<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recluta Online</title>

    <link rel="icon" sizes="192x192" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('img/favicon.png') }}" type="image/png" />

    @include('layouts.adminlte.top')

</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span class="h1"><b>Recluta Online</b></span>
            </div>
            <div class="card-body">
                <x-alerts />
                @yield('content')
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    @include('layouts.adminlte.bottom')
</body>

</html>
