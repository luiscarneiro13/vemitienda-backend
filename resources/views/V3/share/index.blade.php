<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">

    <link rel="icon" type="image/x-icon" href="{{ env('APP_URL') . '/' . $company->logo->url }}">

    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $company->name }}</title>
    <meta property="og:title" content="{{ $company->name }}" />
    <meta name="description" content="{{ $company->slogan }}" />
    <meta property="og:description" content="{{ $company->slogan }}" />
    <meta property="og:image" content="{{ $company->logo ? env('APP_URL') . '/' . $company->logo->url : '' }}" />
    <meta property="og:type" content="article" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/jquery-ui.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plantillas/ogani-master/css/custom.css?v=' . $version) }}" type="text/css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9259103767509807"
        crossorigin="anonymous"></script>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>
    {{-- {{ url('catalog/'.$company->slug) }} --}}
    {{-- {{ $company->theme }} --}}
    <!-- Humberger Begin -->
    <div class="humberger__menu__overlay"></div>
    <div class="humberger__menu__wrapper">
        <div class="humberger__menu__logo">
            <a href="#">
                <img src="{{ $company->logo ? env('APP_URL') . '/' . $company->logo->url : '' }}" alt="">
            </a>
        </div>
        {{-- <div class="humberger__menu__cart">
            <ul>
                <li><a href="#"><i class="fa fa-shopping-bag"></i> <span>3</span></a></li>
            </ul>
            <div class="header__cart__price">item: <span>$150.00</span></div>
        </div> --}}
        <nav class="humberger__menu__nav mobile-menu">
            <ul>
                <li class="active"><a href="#">{{ $company->name }}</a></li>
            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
        <!-- <div class="header__top__right__social">
            <a href="#"><i class="fa fa-facebook"></i></a>
            <a href="#"><i class="fa fa-twitter"></i></a>
            <a href="#"><i class="fa fa-linkedin"></i></a>
            <a href="#"><i class="fa fa-pinterest-p"></i></a>
        </div> -->
        <div class="humberger__menu__contact">
            <ul>
                {{-- <li><i class="fa fa-envelope"></i> {{ $company->user->email }}</li> --}}
                <li>{{ $company->slogan }}</li>
            </ul>
        </div>
    </div>
    <!-- Humberger End -->

    <!-- Header Section Begin -->
    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="header__top__left">
                            <ul>
                                {{-- <li><i class="fa fa-envelope"></i> {{ $company->user->email }}</li> --}}
                                <li>{{ $company->slogan }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="header__logo">
                        <a href="#">
                            <img src="{{ $company->logo ? env('APP_URL') . '/' . $company->logo->url : '' }}"
                                alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">

                </div>
                <div class="col-lg-3">
                    {{-- <div class="header__cart">
                        <ul>
                            <li><a href="#"><i class="fa fa-shopping-bag"></i> <span>3</span></a></li>
                        </ul>
                        <div class="header__cart__price">item: <span>$150.00</span></div>
                    </div> --}}
                </div>
            </div>
            <div class="humberger__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header Section End -->
    {{-- No borrar esta lpinea --}}

    <!-- Hero Section Begin -->
    <section id="hero" class="hero">
        <div id="topeScroll"></div>
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-3">
                    <div class="hero__categories">
                        <div class="hero__categories__all {{ $company->theme->name }}">
                            <i class="fa fa-bars"></i>
                            <span>Categorías</span>
                        </div>
                        <ul style="display: none; background:'white'">
                            @if (@count($categories) > 0)
                                <li><a href="{{ url('catalogo/' . $company->slug) }}">Todos los productos</a></li>
                                @foreach ($categories as $category)
                                    <li><a
                                            href="{{ url('catalogo/' . $company->slug . '?cat=' . $category->id) }}">{{ $category->name }}</a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="hero__search">
                        <div class="hero__search__form">
                            <form>
                                <input id="query" name="query" type="text" placeholder="Buscar por nombre">
                                <button class="site-btn {{ $company->theme->name }}">Buscar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->


    <!-- Featured Section Begin -->
    <section class="">
        <div class="container">
            <div class="row featured__filter" id="product-container">
                @include('V3.share.data')
            </div>
            <div id="loader" style="display: none;" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Cargando más...</span>
                </div>
            </div>
        </div>
    </section>
    <!-- Featured Section End -->

    <section>
        <div class="container">
            <div class="row featured__filter">
                <div class="col-12 mb-5">
                    <center><img width="200px" src="{{ asset('img/sistelconet.webp') }}" alt=""></center>
                </div>
            </div>
        </div>
    </section>
    <!-- Js Plugins -->

    <script src="{{ asset('plantillas/ogani-master/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/main.js') }}"></script>

    <script>
        // Obtén la sección hero
        const topeScroll = document.getElementById('topeScroll');
        const heroSection = document.getElementById('hero');

        // Agrega la clase 'scrolled' cuando la sección hero llegue al top de la pantalla
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop >= topeScroll.offsetTop) {
                heroSection.classList.add('scrolled');
            } else {
                heroSection.classList.remove('scrolled');
            }
        });

        /*Infinite Scroll Script*/
        let page = 1;
        let isLoading = false;

        window.onscroll = function() {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200 && !isLoading) {
                page++;
                loadMoreData(page);
            }
        };

        function loadMoreData(page) {
            const url = "{{ url('catalogo/' . $slug) }}" + "/" + '?page=' + page;
            isLoading = true; // Indicar que se está cargando más data
            $.ajax({
                    url,
                    type: 'get',
                    beforeSend: function() {
                        // Mostrar el spinner antes de cargar los datos
                        $('#loader').show();
                    }
                })
                .done(function(data) {
                    if (data.html === "") {
                        $('#loader').hide();
                        isLoading = false; // Indicar que se terminó de cargar
                        return;
                    }
                    $('#product-container').append(data);
                    $('#loader').hide();
                    isLoading = false; // Indicar que se terminó de cargar
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occurred');
                    $('#loader').hide();
                    isLoading = false; // Indicar que se terminó de cargar
                });
        }
    </script>

</body>

</html>
