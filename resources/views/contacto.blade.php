<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Create a stylish landing page for your business startup and get leads for the offered vemitienda with this HTML landing page template." />
    <meta name="author" content="Inovatik" />

    <!-- OG Meta Tags to improve the way the post looks when you share the page on LinkedIn, Facebook, Google+ -->
    <meta property="og:site_name" content="" />
    <!-- website name -->
    <meta property="og:site" content="" />
    <!-- website link -->
    <meta property="og:title" content="" />
    <!-- title shown in the actual shared post -->
    <meta property="og:description" content="" />
    <!-- description shown in the actual shared post -->
    <meta property="og:image" content="" />
    <!-- image link, make sure it's jpg -->
    <meta property="og:url" content="" />
    <!-- where do you want your post to link to -->
    <meta property="og:type" content="article" />

    <!-- Website Title -->
    <title>Ve mi Tienda</title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,400i,600,700,700i&amp;subset=latin-ext"
        rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/bootstrap.css')}}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/fontawesome-all.css')}}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/swiper.css')}}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/magnific-popup.css')}}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/styles.css')}}" rel="stylesheet" />

    <!-- Favicon  -->
    <link rel="icon" href="{{ asset('plantillas/evolo/images/logo.png') }}" />
</head>

<body data-spy="scroll" data-target=".fixed-top">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top top-nav-collapse">
        <!-- Text Logo - Use this if you don't have a graphic logo -->
        <!-- <a class="navbar-brand logo-text page-scroll" href="index.html">Evolo</a> -->

        <!-- Image Logo -->
        <a class="text-center" href="index.html">
            <img id="logo" height="60px" src="img/logo2.png" alt="">
        </a>

        <!-- Mobile Menu Toggle Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-awesome fas fa-bars"></span>
            <span class="navbar-toggler-awesome fas fa-times"></span>
        </button>
        <!-- end of mobile menu toggle button -->

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a style="color:#333" class="nav-link page-scroll" href="{{ url('/') }}">
                        Volver al Inicio <span class="sr-only">(current)</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div id="contacto" class="form-1">
        @if (Session::has('message'))
        <h5 class="text-center text-{{ Session::get('color') }}">
            Recibimos tu mensaje, pronto nos pondremos en contacto
        </h5>
        @endif
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="text-container">
                        <h2 class="text-center">Contáctanos mediante el formulario</h2>
                        <p>
                            O escríbenos un correo a:
                            <strong>vemitienda@gmail.com</strong> para ofrecerte la
                            información y medios de pago
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form action="{{ route('contact') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-container">
                            <form id="requestForm" data-toggle="validator" data-focus="false">
                                <div class="form-group">
                                    <input type="text" class="form-control-input" id="rname"
                                        name="name" disabled="true" />
                                    <label class="label-control" for="rname">Nombre y Apellido</label>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('name'))
                                        <strong>{{ Session::get('errors')->first('name') }}</strong>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control-input"
                                        id="remail" name="email" disabled="true" />
                                    <label class="label-control" for="remail">Email</label>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('email'))
                                        <strong>{{ Session::get('errors')->first('email') }}</strong>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control-input" id="rphone"
                                        name="phone" disabled="true" />
                                    <label class="label-control" for="rphone">Teléfono</label>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('phone'))
                                        <strong>{{ Session::get('errors')->first('phone') }}</strong>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control-input" id="rmessage" name="message" disabled="true"></textarea>
                                    <label class="label-control" for="rmessage">Mensaje</label>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('message'))
                                        <strong>{{ Session::get('errors')->first('message') }}</strong>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    {{-- <button type="submit" class="form-control-submit-button">
                                        Enviar
                                    </button> --}}

                                    {{-- <a href="mailto:ejemplo@dominio.com?subject=Consulta desde el sitio web&body=Nombre: %0D%0A Apellido: %0D%0A Email: %0D%0A Teléfono: %0D%0A Mensaje: ">
                                        Enviar
                                    </a> --}}
                                </div>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p class="p-small">
                        Copyright © 2023 <a href="https://vemitienda.com.ve">Ve mi Tienda</a> - Todos los derechos
                        reservados
                    </p>
                </div>
                <!-- end of col -->
            </div>
            <!-- enf of row -->
        </div>
        <!-- end of container -->
    </div> --}}

    <!-- Scripts -->
    <script src="{{ asset('plantillas/evolo/js/jquery.min.js')}}"></script>
    <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="{{ asset('plantillas/evolo/js/popper.min.js')}}"></script>
    <!-- Popper tooltip library for Bootstrap -->
    <script src="{{ asset('plantillas/evolo/js/bootstrap.min.js')}}"></script>
    <!-- Bootstrap framework -->
    <script src="{{ asset('plantillas/evolo/js/jquery.easing.min.js')}}"></script>
    <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="{{ asset('plantillas/evolo/js/swiper.min.js')}}"></script>
    <!-- Swiper for image and text sliders -->
    <script src="{{ asset('plantillas/evolo/js/jquery.magnific-popup.js')}}"></script>
    <!-- Magnific Popup for lightboxes -->
    <script src="{{ asset('plantillas/evolo/js/validator.min.js')}}"></script>
    <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="{{ asset('plantillas/evolo/js/scripts.js')}}"></script>
    <!-- Custom scripts -->

    <script>
        $(function() {
    // esta parte es la que controla cuando se mueve el scroll y ejecuta una función
            $(document).scroll(function() {
                // aca se pregunta si el scroll se movio de pa bajo.
            if ($(this).scrollTop() > 1) {
                // esta parte cambia el atributo "src" de la etiqueta "img"
                $('#logo').attr('src', "img/logo2.png")
            }
            if ($(this).scrollTop() < 1) {
                $('#logo').attr('src', "img/logo2blanco.png" ); } });
            });
    </script>
</body>

</html>
