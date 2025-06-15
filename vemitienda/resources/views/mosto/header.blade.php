<header class="header-section header-cl-black">
    <div class="container">
        <div class="header-wrapper">
            <div>
                <a href="#">
                    <img height="70px" src="{{ asset('img/logoMenu.webp') }}" alt="logo">
                </a>
            </div>
            <ul class="menu">
                <li><a href="{{ url('/') }}#home">Inicio</a></li>
                <li><a href="{{ url('/') }}#features">Características</a></li>
                <li><a href="{{ url('/') }}#yourCustomers">Tus Clientes</a></li>
                <li><a href="{{ url('/') }}#testimonies">Testimonios</a></li>
                <li><a href="{{ url('/') }}/contacto">Contacto</a></li>
                {{-- <li><a href="{{ url('/') }}/blog">Blog</a></li> --}}
            </ul>
            <div class="header-bar d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</header>
