<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Ve mi Tienda</title>
    <link rel="icon" href="{{ asset('img/logometa.webp') }}" />

    <!-- SEO Meta Tags -->
    <meta name="description" content="Crea tu propia tienda online" />
    <meta name="author" content="Inovatik" />

    <!-- OG Meta Tags to improve the way the post looks when you share the page on LinkedIn, Facebook, Google+ -->
    <meta property="og:site_name" content="Ve mi Tienda" />
    <!-- website name -->
    <meta property="og:site" content="{{ url('/') }}" />
    <!-- website link -->
    <meta property="og:title" content="Ve mi Tienda" />
    <!-- title shown in the actual shared post -->
    <meta property="og:description" content="Crea tu propia tienda online" />
    <!-- description shown in the actual shared post -->
    <meta property="og:image" content="{{ asset('img/logometa.webp') }}" />
    <!-- image link, make sure it's jpg -->
    <meta property="og:url" content="{{ url('/') }}" />
    <!-- where do you want your post to link to -->
    <meta property="og:type" content="article" />
    <x-googleAnalytics />
    <x-googleAdsense />
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('plantillas/mosto/css/main.css') }}">
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LeQLPAnAAAAAPM-fhlGuSrejjofH3B-DuvY8F51"></script>
    <script>

        document.addEventListener('submit', function(e) {

            e.preventDefault();

            grecaptcha.enterprise.ready(async () => {

                const token = await grecaptcha.enterprise.execute('6Lc4ovAnAAAAACerisb_PVs3fa28jnN3WlX54UNF', {
                    action: 'LOGIN'
                });

                let form = e.target;
                let input = document.createElement('input');

                input.type = 'hidden';
                input.name = 'g-recaptcha-response'
                input.value = token

                form.appendChild(input)
                form.submit()
            });
        })
    </script>
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
    @include('mosto.headerContact')
    <!--============= Header Section Ends Here =============-->


    <!--============= Header Section Ends Here =============-->
    <section class="page-header single-header bg_img oh" data-background="{{ asset('img/page-header1.webp') }}">
        <div class="bottom-shape d-none d-md-block">
            <img src="{{ asset('img/page-header.webp') }}" alt="css">
        </div>
    </section>
    <!--============= Header Section Ends Here =============-->



    <!--============= Contact Section Starts Here =============-->
    <section class="contact-section padding-top padding-bottom">
        <div class="container">
            <div class="section-header mw-100 cl-white">
                <h2 class="title">Contáctanos</h2>
                <p>Atenderemos sus solicitudes y dudas</p>
            </div>
            <div class="row justify-content-center justify-content-lg-between">
                <div class="col-lg-7">
                    <div class="contact-wrapper">
                        @if (Session::has('message'))
                            <h5 class="text-center text-success mb-5">¡Gracias por escribirnos, ya recibimos tu mensaje
                                y
                                pronto nos pondremos en
                                contacto contigo!</h5>
                            <div class="text-center">
                                <img src="{{ asset('img/icon/counter5.webp') }}" alt="">
                            </div>
                            <br><br><br>
                        @else
                            <h4 class="title text-center mb-30">Formulario</h4>
                            <form id="form" action="{{ route('contact') }}" method="POST" autocomplete="off"
                                class="contact-form">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Nombre y Apellido</label>
                                    <input type="text" name="name" id="rname" value="{{ old('name') }}"
                                        placeholder="Ingresa tu nombre y apellido" required>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('name'))
                                            <strong>{{ Session::get('errors')->first('name') }}</strong>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name">Email</label>
                                    <input type="text" name="email" id="remail" value="{{ old('email') }}"
                                        placeholder="Ingresa tu correo electrónico" required>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('email'))
                                            <strong>{{ Session::get('errors')->first('email') }}</strong>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Teléfono</label>
                                    <input type="text" name="phone" id="rphone" value="{{ old('phone') }}"
                                        placeholder="Ingresa tu número de teléfono" required>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('phone'))
                                            <strong>{{ Session::get('errors')->first('phone') }}</strong>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label for="message">Mensaje</label>
                                    <textarea id="rmessage" name="message" placeholder="Escribe tu mensaje" required></textarea>
                                    <div class="help-block with-errors" style="color:#F6442C">
                                        @if (Session::has('errors') && @Session::get('errors')->first('message'))
                                            <strong>{{ Session::get('errors')->first('message') }}</strong>
                                        @endif
                                    </div>
                                </div>

                                <span id="msg"></span>

                                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>

                                <div class="form-group">
                                    <button class="g-recaptcha"
                                        data-sitekey="6Lc4ovAnAAAAACerisb_PVs3fa28jnN3WlX54UNF"
                                        data-callback='onSubmit' data-action='submit' type="submit">Enviar
                                        Mensaje</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="contact-content">
                        <div class="man d-lg-block d-none">
                            <img src="{{ asset('img/contact/man.webp') }}" alt="bg">
                        </div>
                        {{-- <div class="section-header left-style">
                            <h3 class="title">Have questions?</h3>
                            <p>Have questions about payments or price
                                plans? We have answers!</p>
                            <a href="#0">Read F.A.Q <i class="fas fa-angle-right"></i></a>
                        </div> --}}
                        <div class="contact-area">
                            <div class="contact-item">
                                <div class="contact-thumb">
                                    <img src="{{ asset('img/contact/contact1.webp') }}" alt="contact">
                                </div>
                                <div class="contact-contact">
                                    <h5 class="subtitle">Email</h5>
                                    <a href="Mailto:info@mosto.com">info@vemitienda.online</a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-thumb">
                                    <img src="{{ asset('img/contact/contact2.webp') }}" alt="contact">
                                </div>
                                <div class="contact-contact">
                                    <h5 class="subtitle">Whatsapp</h5>
                                    <a href="Tel:565656855">+58 (0424) 849-68-99</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============= Contact Section Ends Here =============-->

    <!--============= Footer Section Starts Here =============-->
    @include('mosto.footerContact')
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
    {{-- <script src="https://maps.google.com/maps/api/js?key=AIzaSyCo_pcAdFNbTDCAvMwAD19oRTuEmb9M50c"></script> --}}
    <script src="{{ asset('plantillas/mosto/js/map.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/contact.js') }}"></script>
    <script src="{{ asset('plantillas/mosto/js/main.js') }}"></script>


</body>

</html>
