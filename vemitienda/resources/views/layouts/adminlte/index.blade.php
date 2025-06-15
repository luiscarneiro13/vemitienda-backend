<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ve mi Tienda</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" sizes="192x192" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('img/favicon.png') }}" type="image/png" />

    @include('layouts.adminlte.top')

    <style>
        .page-item.active .page-link {
            background-color: #343a40;
            border-color: #343a40;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #343a40;
            border-color: #343a40;
        }

        .alert-success {
            background-color: #1B998B;
            border-color: #1B998B;
        }
        .no-disponible{
            color: #D7263D;
        }
        #toast-container .toast-success {
            background-color: #1B998B;
        }


    </style>
</head>

<body class="sidebar-mini layout-fixed">
    <div class="wrapper">

        @include('layouts.adminlte.navbar')

        @include('layouts.adminlte.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div id="app" class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('titulo')</h1>
                        </div>
                        {{-- <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Simple Tables</li>
                            </ol>
                        </div> --}}
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @include('layouts.adminlte.footer')
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    @include('layouts.adminlte.bottom')
    @toastr_js
    @yield('js')

    {{-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <script src="{{ asset('js/app.js?v='.$version) }}"></script> --}}

</body>

</html>
