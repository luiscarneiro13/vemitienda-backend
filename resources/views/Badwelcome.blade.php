<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- SEO Meta Tags -->
    <meta name="description" content="Crea tu propia tienda online gratis" />
    <meta name="author" content="Inovatik" />

    <!-- OG Meta Tags to improve the way the post looks when you share the page on LinkedIn, Facebook, Google+ -->
    <meta property="og:site_name" content="Ve mi Tienda" />
    <!-- website name -->
    <meta property="og:site" content="{{ url('/') }}" />
    <!-- website link -->
    <meta property="og:title" content="Ve mi Tienda" />
    <!-- title shown in the actual shared post -->
    <meta property="og:description" content="Crea tu propia tienda online gratis" />
    <!-- description shown in the actual shared post -->
    <meta property="og:image" content="{{ asset('img/logometa.webp') }}" />
    <!-- image link, make sure it's jpg -->
    <meta property="og:url" content="{{ url('/') }}" />
    <!-- where do you want your post to link to -->
    <meta property="og:type" content="article" />

    <!-- Website Title -->
    <title>Ve mi Tienda</title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,400i,600,700,700i&amp;subset=latin-ext"
        rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/fontawesome-all.css') }}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/swiper.css') }}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/magnific-popup.css') }}" rel="stylesheet" />
    <link href="{{ asset('plantillas/evolo/css/styles.css') }}" rel="stylesheet" />
    <style>
        .rojo {
            background-color: #EF4444;
            border: 0.125rem solid #EF4444;
        }

        .verde {
            background-color: #10B981;
            border: 0.125rem solid #10B981;
        }

        .naranja {
            background-color: #F44C04;
            border: 0.125rem solid #F44C04;
        }

        .amarillo {
            background-color: #F59E0B;
            border: 0.125rem solid #F59E0B;
        }

        .gris {
            background-color: #6B7280;
            border: 0.125rem solid #6B7280;
        }

        .azul-anil {
            background-color: #6366F1;
            border: 0.125rem solid #6366F1;
        }

        .purpura {
            background-color: #8B5CF6;
            border: 0.125rem solid #8B5CF6;
        }

        .negro {
            background-color: #000000;
            border: 0.125rem solid #000000;
        }

        .azul {
            background-color: #3B82F6;
            border: 0.125rem solid #3B82F6;
        }
    </style>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Favicon  -->
    <link rel="icon" href="{{ asset('plantillas/evolo/images/logo.png') }}" />
    @include('toastr')
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9259103767509807"
     crossorigin="anonymous"></script>
</head>

<body data-spy="scroll" data-target=".fixed-top">
    <!-- Preloader -->
    <div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <!-- end of preloader -->

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <!-- Text Logo - Use this if you don't have a graphic logo -->
        <!-- <a class="navbar-brand logo-text page-scroll" href="index.html">Evolo</a> -->

        <!-- Image Logo -->
        <a class="text-center" href="index.html">
            <img id="logo" height="60px" src="img/logo2blanco.png" alt="">
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
                    <a class="nav-link page-scroll" href="#header">Inicio <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#funcionamiento">Funcionamiento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#demo">DEMO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#vemitienda">Ve mi Tienda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#precio">Precio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="{{ url('contacto') }}">Contacto</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- end of navbar -->
    <!-- end of navigation -->

    <!-- Header -->
    <header id="header" class="header">
        <div class="header-content">
            <div class="container">
                <div class="row" style="margin-top:-70px">
                    <div class="col-lg-7">
                        <div class="text-container">
                            <div class="text-center">
                                <img height="200px" src="{{ asset('plantillas/evolo/images/logo.png') }}"
                                    alt="alternative" />
                            </div>
                            <h1>
                                <span class="turquoise" style="font-size: 40px">Crea tu propia tienda online gratis</span>
                                <br />30 días gratis
                            </h1>
                            <p class="p-large">
                                Con nuestra aplicación, puedes crear tu propia tienda en línea. Agrega tus productos y
                                recibe pedidos
                            </p>
                            <a class="btn-solid-lg page-scroll mt-1" target="_blank"
                                href="https://play.google.com/store/apps/details?id=com.vemitienda.online">DESCARGAR</a>
                            <a class="btn-solid-lg page-scroll mt-1" target="_blank" href="#demo">DEMO</a>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="mt-5 pt-5 text-right">
                            <img class="img-fluid" src="{{ asset('plantillas/evolo/images/mobile.png') }}"
                                alt="alternative" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- vemitienda -->
    <div id="funcionamiento" class="cards-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mt-5">
                    <h2>¿Cómo funciona?</h2>
                    {{-- <p class="p-heading p-large">
                        Descarga la aplicación, crea tu cuenta e ingresa
                    </p> --}}
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <img class="card-image" src="{{ asset('plantillas/evolo/images/services-icon-1.svg') }}"
                            alt="alternative" />
                        <div class="card-body">
                            <h4 class="card-title">Crea</h4>
                            <div class="text-left">
                                <p># Agrega la información de tu tienda</p>
                                <p># Agrega las categorías de los productos que vendes</p>
                                <p># Agrega tus productos con su imagen, precio y cantidad disponible</p>
                                <p># La aplicación creará tu tienda automáticamente</p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <img class="card-image" src="{{ asset('plantillas/evolo/images/services-icon-3.svg') }}"
                            alt="alternative" />
                        <div class="card-body">
                            <h4 class="card-title">Comparte</h4>
                            <div class="text-left">
                                <p># Comparte solo el catálogo (sin carrito de compras) o tu tienda con carrito de
                                    compras</p>
                                <p># Puedes compartir tu tienda por whatsapp y otras redes sociales, además tendrás un
                                    link permanente de tu tienda</p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <img class="card-image" src="{{ asset('plantillas/evolo/images/services-icon-2.svg') }}"
                            alt="alternative" />
                        <div class="card-body">
                            <h4 class="card-title">Recibe Pedidos</h4>
                            <div class="text-left">
                                <p># Cuando tus clientes entran en tu tienda, podrán ver todos tus productos</p>
                                <p># Además tu tienda contará con un carrito de compras</p>
                                <p># Cuando tu cliente hace un pedido, la aplicación te envía un notificación por correo
                                    con todos los productos que seleccionó el comprador</p>
                                <p># Además, el pedido se verá reflejado en la pantalla de pedidos de la aplicación
                                    móvil</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colocar el Video -->

            {{-- <div class="basic-3">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Video promocional</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <video class="img-fluid" width="960" height="720" controls autoplay>
                                <source src="{{ asset('videos/promocional.mp4') }}" type="video/mp4">
                            </video>

                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    <div id="demo" class="cards-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mt-5">
                    <h2>DEMO</h2>
                    <p class="p-heading p-large">
                        Puede ver los demos en diferentes temas (colores)
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema rojo</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 rojo" target="_blank" href="rojo">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema verde</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 verde" target="_blank" href="verde">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema naranja</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 naranja" target="_blank" href="naranja">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema amarillo</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 amarillo" target="_blank" href="amarillo">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema gris</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 gris" target="_blank" href="gris">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema azul añil</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 azul-anil" target="_blank"
                                    href="azul-anil">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema púrpura</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 purpura" target="_blank" href="purpura">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema negro</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 negro" target="_blank" href="negro">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tema azul</h4>
                            <div class="text-center">
                                <a class="btn-solid-lg page-scroll mt-1 azul" target="_blank" href="azul">Ver
                                    demo</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="vemitienda" class="basic-1">
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-6">
                    <div class="text-container">
                        <h2 class="text-center">¿Porqué usar Ve mi Tienda?</h2>
                        <p>
                            1.- Al agregar tus productos y la información de tu tienda, la aplicación móvil creará
                            automáticamente un sitio web con todos tus productos y un carrito de compras.
                        </p>
                        <p>
                            2.- La aplicación va descontando los productos con cada pedido que te hagan y si tienes
                            productos agotados, los clientes no podrán comprarlos.
                        </p>
                        <p>3.- Tu tienda se adapta a todo tipo de dispositivo.</p>
                        <p>
                            4.- Acceso súper rápido para compartir la tienda, es solo
                            pulsar un botón y se hace la magia.
                        </p>
                    </div>
                    <!-- end of text-container -->
                </div>
                <!-- end of col -->
                <div class="col-lg-6">
                    <div class="image-container">
                        <img class="img-fluid"
                            src="{{ asset('plantillas/evolo/images/details-1-office-worker.svg') }}"
                            alt="alternative" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing -->
    <div id="precio" class="cards-2">
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-12">
                    <h2>¿Y cuál es el precio?</h2>
                    <p class="p-heading p-large">
                        Te damos 30 días gratis para que pruebes la aplicación
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Catálogo</div>
                            <div class="card-subtitle"></div>
                            <hr class="cell-divide-hr" />
                            <div class="price">
                                <span class="currency">$</span><span class="value">1</span>
                                <div class="frequency">Mensualmente</div>
                            </div>
                            <hr class="cell-divide-hr" />
                            <ul class="list-unstyled li-space-lg">
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Productos ilimitados</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Compartir ilimitado</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Catálogo virtual</div>
                                </li>
                                <li class="media">
                                    <strike>
                                        <div class="media-body">Tienda virtual</div>
                                    </strike>
                                </li>
                                <li class="media">
                                    <strike>
                                        <div class="media-body">Carrito de compras</div>
                                    </strike>
                                </li>
                                <li class="media">
                                    <strike>
                                        <div class="media-body">Gestión de pedidos</div>
                                    </strike>
                                </li>
                            </ul>
                            <div class="button-wrapper">
                                <a class="btn-solid-reg page-scroll" href="{{ url('contacto') }}">Lo quiero</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Tienda Virtual</div>
                            <div class="card-subtitle"></div>
                            <hr class="cell-divide-hr" />
                            <div class="price">
                                <span class="currency">$</span><span class="value">2</span>
                                <div class="frequency">Mensualmente</div>
                            </div>
                            <hr class="cell-divide-hr" />
                            <ul class="list-unstyled li-space-lg">
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Productos ilimitados</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Compartir ilimitado</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Catálogo virtual</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Tienda virtual</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Carrito de compras</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Gestión de pedidos</div>
                                </li>
                            </ul>
                            <div class="button-wrapper">
                                <a class="btn-solid-reg page-scroll" href="{{ url('contacto') }}">Lo quiero</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>

    <!-- Cuando se quiera colocar los testimonios -->
    <!-- <div class="slider-2">
      <div class="container">
        <div class="row">
          <div class="col-lg-6">
            <div class="image-container">
              <img
                class="img-fluid"
                src="images/testimonials-2-men-talking.svg"
                alt="alternative"
              />
            </div>
          </div>
          <div class="col-lg-6">
            <h2>Testimonials</h2>

            <div class="slider-container">
              <div class="swiper-container card-slider">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <div class="card">
                      <img
                        class="card-image"
                        src="images/testimonial-1.svg"
                        alt="alternative"
                      />
                      <div class="card-body">
                        <p class="testimonial-text">
                          I just finished my trial period and was so amazed with
                          the support and results that I purchased Evolo right
                          away at the special price.
                        </p>
                        <p class="testimonial-author">Jude Thorn - Designer</p>
                      </div>
                    </div>
                  </div>

                  <div class="swiper-slide">
                    <div class="card">
                      <img
                        class="card-image"
                        src="images/testimonial-2.svg"
                        alt="alternative"
                      />
                      <div class="card-body">
                        <p class="testimonial-text">
                          Evolo has always helped or startup to position itself
                          in the highly competitive market of mobile
                          applications. You will not regret using it!
                        </p>
                        <p class="testimonial-author">
                          Marsha Singer - Developer
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="swiper-slide">
                    <div class="card">
                      <img
                        class="card-image"
                        src="images/testimonial-3.svg"
                        alt="alternative"
                      />
                      <div class="card-body">
                        <p class="testimonial-text">
                          Love their vemitienda and was so amazed with the support
                          and results that I purchased Evolo for two years in a
                          row. They are awesome.
                        </p>
                        <p class="testimonial-author">Roy Smith - Marketer</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- Para habilitar el footer -->
    <!-- <div class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="footer-col middle">
              <h4>Important Links</h4>
              <ul class="list-unstyled li-space-lg">
                <li class="media">
                  <i class="fas fa-square"></i>
                  <div class="media-body">
                    Our business partners
                    <a class="turquoise" href="#your-link">startupguide.com</a>
                  </div>
                </li>
                <li class="media">
                  <i class="fas fa-square"></i>
                  <div class="media-body">
                    Read our
                    <a class="turquoise" href="terms-conditions.html"
                      >Terms & Conditions</a
                    >,
                    <a class="turquoise" href="privacy-policy.html"
                      >Privacy Policy</a
                    >
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-6">
            <div class="footer-col last">
              <h4>Social Media</h4>
              <span class="fa-stack">
                <a href="#your-link">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="fab fa-facebook-f fa-stack-1x"></i>
                </a>
              </span>
              <span class="fa-stack">
                <a href="#your-link">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="fab fa-twitter fa-stack-1x"></i>
                </a>
              </span>
              <span class="fa-stack">
                <a href="#your-link">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="fab fa-google-plus-g fa-stack-1x"></i>
                </a>
              </span>
              <span class="fa-stack">
                <a href="#your-link">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="fab fa-instagram fa-stack-1x"></i>
                </a>
              </span>
              <span class="fa-stack">
                <a href="#your-link">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="fab fa-linkedin-in fa-stack-1x"></i>
                </a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="p-small">
                        <a href="{{ url('politica') }}">Política de privacidad</a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p class="p-small">
                        Copyright © 2023 <a href="https://vemitienda.online">Ve mi Tienda</a> - Todos los derechos
                        reservados
                    </p>
                </div>
                <!-- end of col -->
            </div>
            <!-- enf of row -->
        </div>
        <!-- end of container -->
    </div>
    <!-- end of copyright -->
    <!-- end of copyright -->

    <!-- Scripts -->
    <script src="{{ asset('plantillas/evolo/js/jquery.min.js') }}"></script>
    <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="{{ asset('plantillas/evolo/js/popper.min.js') }}"></script>
    <!-- Popper tooltip library for Bootstrap -->
    <script src="{{ asset('plantillas/evolo/js/bootstrap.min.js') }}"></script>
    <!-- Bootstrap framework -->
    <script src="{{ asset('plantillas/evolo/js/jquery.easing.min.js') }}"></script>
    <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="{{ asset('plantillas/evolo/js/swiper.min.js') }}"></script>
    <!-- Swiper for image and text sliders -->
    <script src="{{ asset('plantillas/evolo/js/jquery.magnific-popup.js') }}"></script>
    <!-- Magnific Popup for lightboxes -->
    <script src="{{ asset('plantillas/evolo/js/validator.min.js') }}"></script>
    <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="{{ asset('plantillas/evolo/js/scripts.js') }}"></script>
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
                    $('#logo').attr('src', "img/logo2blanco.png");
                }
            });
        });
    </script>
</body>

</html>
