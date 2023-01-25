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
    <title>Evolo - StartUp HTML Landing Page Template</title>

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
            <img height="60px" src="{{ asset('plantillas/evolo/images/logo.png') }}" alt="">
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
                    <a class="nav-link page-scroll" href="#vemitienda">Ve mi Tienda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#precio">Precio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#contacto">Contacto</a>
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
                <div class="row" style="margin-top:-200px">
                    <div class="col-lg-6">
                        <div class="text-container">
                            <div class="text-center">
                                <img height="200px" src="{{ asset('plantillas/evolo/images/logo.png') }}"
                                    alt="alternative" />
                            </div>
                            <h1>
                                <span class="turquoise" style="font-size: 40px">Tu catálogo automático</span>
                                <br />30 días gratis
                            </h1>
                            <p class="p-large">
                                Con nuestra aplicación, puedes cargar tus productos y
                                compartir el enlace de tu catálogo online por redes sociales,
                                email, entre otros.
                            </p>
                            <a class="btn-solid-lg page-scroll" href="#funcionamiento">¿Cómo funciona?</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mt-5 pt-5 text-center">
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
                    <p class="p-heading p-large">
                        Descarga la aplicación, crea tu cuenta e ingresa
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <img class="card-image" src="{{ asset('plantillas/evolo/images/services-icon-1.svg') }}"
                            alt="alternative" />
                        <div class="card-body">
                            <h4 class="card-title">Crea</h4>
                            <p>Puedes agregar productos con su respectiva imagen</p>
                        </div>
                    </div>

                    <div class="card">
                        <img class="card-image" src="{{ asset('plantillas/evolo/images/services-icon-2.svg')}}"
                            alt="alternative" />
                        <div class="card-body">
                            <h4 class="card-title">Personaliza</h4>
                            <p>Cambia el color de fondo de tu catálogo online</p>
                        </div>
                    </div>

                    <div class="card">
                        <img class="card-image" src="{{ asset('plantillas/evolo/images/services-icon-3.svg')}}"
                            alt="alternative" />
                        <div class="card-body">
                            <h4 class="card-title">Comparte</h4>
                            <p>
                                Crearemos un catálogo de forma automática para ti, el cual
                                podrás compartir con tus clientes
                            </p>
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
                        <h2>¿Porqué usar Ve mi Tienda?</h2>
                        <p>
                            1.- Creamos un catálogo online para ti, automáticamente en menos
                            de 1 minuto.
                        </p>
                        <p>
                            2.- Si quieres quitar productos del catálogo, solo deberás
                            seleccionar la opción de no mostrar. Tu producto queda
                            almacenado y en cualquier otro momento lo puedes mostrar de
                            nuevo, ya no será necesario volver a crear un catálogo solo para
                            quitar o agregar productos.
                        </p>
                        <p>3.- El catálogo se adapta a todo tipo de dispositivo.</p>
                        <p>
                            4.- Acceso súper rápido para compartir el catálogo, es solo
                            pulsar un botón y se hace la magia.
                        </p>
                        <p>5.- El catálogo es tuyo y estará siempre en línea.</p>
                    </div>
                    <!-- end of text-container -->
                </div>
                <!-- end of col -->
                <div class="col-lg-6">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('plantillas/evolo/images/details-1-office-worker.svg')}}"
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
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">PREMIUM</div>
                            <div class="card-subtitle"></div>
                            <hr class="cell-divide-hr" />
                            <div class="price">
                                <span class="currency">$</span><span class="value">10</span>
                                <div class="frequency">Anualmente</div>
                            </div>
                            <hr class="cell-divide-hr" />
                            <ul class="list-unstyled li-space-lg">
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Hasta 100 productos</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i>
                                    <div class="media-body">Compartir ilimitado</div>
                                </li>
                            </ul>
                            <div class="button-wrapper">
                                <a class="btn-solid-reg page-scroll" href="#request">Lo quiero</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="contacto" class="form-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="text-container">
                        <h2>Contáctanos mediante el formulario</h2>
                        <p>
                            O escríbenos un correo a:
                            <strong>info@vemitienda.online</strong> para ofrecerte la
                            información y medios de pago
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-container">
                        <form id="requestForm" data-toggle="validator" data-focus="false">
                            <div class="form-group">
                                <input type="text" class="form-control-input" id="rname" name="rname" required />
                                <label class="label-control" for="rname">Nombre y Apellido</label>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control-input" id="remail" name="remail" required />
                                <label class="label-control" for="remail">Email</label>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control-input" id="rphone" name="rphone" required />
                                <label class="label-control" for="rphone">Teléfono</label>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="form-control-submit-button">
                                    Enviar
                                </button>
                            </div>
                            <div class="form-message">
                                <div id="rmsgSubmit" class="h3 text-center hidden"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuando se quiera colocar el Video -->

    <!-- <div class="basic-3">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <h2>Check Out The Video</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="image-container">
              <div class="video-wrapper">
                <a
                  class="popup-youtube"
                  href="https://www.youtube.com/watch?v=fLCjQJCekTs"
                  data-effect="fadeIn"
                >
                  <img
                    class="img-fluid"
                    src="images/video-frame.svg"
                    alt="alternative"
                  />
                  <span class="video-play-button">
                    <span></span>
                  </span>
                </a>
              </div>
            </div>

            <p>
              This video will show you a case study for one of our
              <strong>Major Customers</strong> and will help you understand why
              your startup needs Evolo in this highly competitive market
            </p>
          </div>
        </div>
      </div>
    </div> -->

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
</body>

</html>
