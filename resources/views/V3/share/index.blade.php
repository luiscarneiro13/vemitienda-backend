<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">

    <link rel="icon" type="image/x-icon" href="{{ env('APP_URL') . '/' . @$company->logo->url }}">

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
    <div class="humberger__menu__overlay"></div>
    <div class="humberger__menu__wrapper">
        <div class="humberger__menu__logo">
            <a href="#">
                <img src="{{ $company->logo ? env('APP_URL') . '/' . $company->logo->url : '' }}" alt="">
            </a>
        </div>
        <nav class="humberger__menu__nav mobile-menu">
            <ul>
                <li class="active"><a href="#">{{ $company->name }}</a></li>
            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
        <div class="humberger__menu__contact">
            <ul>
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
                                <input id="query" name="query" type="text" placeholder="Buscar por nombre" value="{{ request()->get('query') }}">
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
            <div class="scrolling-pagination">
                <div class="row featured__filter" id="product-container">
                    @foreach ($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mix oranges fresh-meat">
                            <div class="featured__item">
                                <div class="text-center">
                                    <image class="img-fluid"
                                        src="{{ count(@$product->image) > 0 ? env('APP_URL') . '/' . $product->image[0]->url : '' }}" />
                                </div>
                                <div class="featured__item__text">
                                    <h6><a href="#">{{ $product->name }}</a></h6>
                                    @if ($product->price > 0)
                                        <h5>${{ $product->price }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{ $products->links() }}
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
    <script src="{{ asset('plantillas/ogani-master/js/jquery.jscroll.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('plantillas/ogani-master/js/main.js') }}"></script>

    <script type="text/javascript">
        $('ul.pagination').hide();
        $(function() {
            $('.scrolling-pagination').jscroll({
                autoTrigger: true,
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.scrolling-pagination',
                callback: function() {
                    $('ul.pagination').remove();
                }
            });
        });
    </script>
</body>

</html>
