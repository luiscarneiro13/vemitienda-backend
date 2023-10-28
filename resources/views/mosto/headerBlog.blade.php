<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Blog</title>
    @yield('metaBlog')

    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <style>
        .header-section.header-cl-black.active .header-wrapper .menu li a {
            color: #3b368c;
        }

        .header-section.header-cl-black .header-wrapper .menu li a {
            color: #fff;
        }
    </style>

</head>

<body data-spy="scroll" data-target="#faq-menu" data-offset="150">
    <!--============= ScrollToTop Section Starts Here =============-->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <a href="#0" class="scrollToTop"><i class="fas fa-angle-up"></i></a>
    <div class="overlay"></div>
    <!--============= ScrollToTop Section Ends Here =============-->


    <!--============= Header Section Starts Here =============-->
    @include('mosto.header')
    <!--============= Header Section Ends Here =============-->


    <!--============= Header Section Ends Here =============-->
    <section class="page-header bg_img oh" data-background="{{ asset('img/page-header1.webp') }}">
        <div class="bottom-shape d-none d-md-block">
            <img src="{{ asset('img/page-header.webp') }}" alt="css">
        </div>
        <div class="container">
            <div class="page-header-content cl-white">
                <h2 class="title">Blog Ve mi Tienda</h2>
            </div>
        </div>
    </section>
    <!--============= Header Section Ends Here =============-->
