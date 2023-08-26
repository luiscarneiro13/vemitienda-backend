<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Ve mi Tienda</title>
    <link rel="icon" href="{{ asset('plantillas/evolo/images/logo.png') }}" />

    <!-- SEO Meta Tags -->
    <meta name="description" content="Crea tu propia tienda online" />
    <meta name="author" content="Inovatik" />

    <!-- OG Meta Tags to improve the way the post looks when you share the page on LinkedIn, Facebook, Google+ -->
    <meta property="og:site_name" content="Ve mi Tienda" />
    <!-- website name -->
    <meta property="og:site" content="https://vemitienda.online" />
    <!-- website link -->
    <meta property="og:title" content="Ve mi Tienda" />
    <!-- title shown in the actual shared post -->
    <meta property="og:description" content="Crea tu propia tienda online" />
    <!-- description shown in the actual shared post -->
    <meta property="og:image" content="https://vemitienda.online/img/logometa.png" />
    <!-- image link, make sure it's jpg -->
    <meta property="og:url" content="https://vemitienda.online" />
    <!-- where do you want your post to link to -->
    <meta property="og:type" content="article" />

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

</head>

<body>
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


    <!--============= LISTO Header Section Starts Here =============-->
    @include('mosto.header')
    <!--============= Header Section Ends Here =============-->


    <!--============= LISTO Banner Section Starts Here =============-->
    @include('mosto.banner', ['visits' => $visits])
    <!--============= Banner Section Ends Here =============-->


    <!--============= LISTO Exclusive Section Starts Here =============-->
    @include('mosto.features')
    <!--============= Exclusive Section Ends Here =============-->


    <!--============= LISTO Colaboration Section Starts Here =============-->
    @include('mosto.yourCustomers')
    <!--============= Colaboration Section Ends Here =============-->


    <!--============= Smart Watch Section Starts Here =============-->
    {{-- @include('mosto.smartWatch') --}}
    <!--============= Smart Watch Section Ends Here =============-->


    {{-- <!--============= LISTO Pricing Section Starts Here =============-->
    @include('mosto.plans')
    <!--============= Pricing Section Ends Here =============--> --}}


    <!--============= Newslater Section Starts Here =============-->
    {{-- @include('mosto.newsLater') --}}
    <!--============= Newslater Section Ends Here =============-->


    <!--============= Coverage Section Starts Here =============-->
    {{-- @include('mosto.coverage') --}}
    <!--============= Coverage Section Ends Here =============-->


    <!--============= LISTO Testimonial Section Starts Here =============-->
    @include('mosto.testimonies', ['testimonies' => $testimonies])
    <!--============= Testimonial Section Ends Here =============-->


    <!--============= Faq Section Starts Here =============-->
    {{-- @include('mosto.faq') --}}
    <!--============= Faq Section Ends Here =============-->


    <!--============= LISTO Trial Section Starts Here =============-->
    @include('mosto.trial')
    <!--============= Trial Section Ends Here =============-->


    <!--============= Footer Section Starts Here =============-->
    @include('mosto.footer')
    <!--============= Footer Section Ends Here =============-->

    <script src="{{ asset('plantillas/mosto/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/modernizr-3.6.0.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/plugins.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/wow.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/waypoints.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/nice-select.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/owl.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/counterup.min.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/paroller.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/main.js') }}"></script>
</body>

</html>
