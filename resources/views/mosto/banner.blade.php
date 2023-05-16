<section class="banner-4 bg_img oh bottom_left"
    data-background="{{ asset('img/banner-bg-4.png') }}">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-7">
                <div class="banner-content-3">
                    <h1 class="title">Crea tu propia tienda online</h1>
                    <p>Con nuestra aplicación, puedes crear tu propia tienda en línea. Agrega tus productos y recibe
                        pedidos</p>
                    <div class="banner-button-group">
                        <a target="_blank" href="https://play.google.com/store/apps/details?id=com.vemitienda.online" class="button-4">Descargar</a>
                        {{-- <a href="https://www.youtube.com/watch?v=ObZwFExwzOo" class="play-button popup">
                            <img src="{{ asset('plantillas/mosto/images/button/play2.png') }}" alt="button">
                            <span class="cl-black">Ver demo</span>
                        </a> --}}
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="banner-nav-container bg_img bg_contain bottom_center"
                    data-background="{{ asset('img/banner/banner-4.png') }}">
                    <div class="ban-nav">
                        <a href="#0" class="ban-prev active">
                            <i class="flaticon-left"></i>
                        </a>
                        <a href="#0" class="ban-next">
                            <i class="flaticon-right"></i>
                        </a>
                    </div>
                    <div class="banner-4-slider owl-theme owl-carousel">
                        <div class="slide-item bg_img"
                            data-background="{{ asset('img/banner/slide1.png') }}">
                        </div>
                        <div class="slide-item bg_img"
                            data-background="{{ asset('img/banner/slide2.png') }}">
                        </div>
                        <div class="slide-item bg_img"
                            data-background="{{ asset('img/banner/slide3.png') }}">
                        </div>
                        <div class="slide-item bg_img"
                            data-background="{{ asset('img/banner/slide4.png') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="banner-odometer">
                    <div class="counter-item">
                        <div class="counter-thumb">
                            <img src="{{ asset('img/icon/counter1.png') }}" alt="icon">
                        </div>
                        <div class="counter-content">
                            <h2 class="title"><span class="counter">120</span></h2>
                            <span>Usuarios Premium</span>
                        </div>
                    </div>
                    <div class="counter-item">
                        <div class="counter-thumb">
                            <img src="{{ asset('img/icon/counter2.png') }}" alt="icon">
                        </div>
                        <div class="counter-content">
                            <h2 class="title"><span class="counter">{{ $visits }}</span></h2>
                            <span>Visitas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
