<footer class="footer-section bg_img" data-background="{{ asset('img/footer/footer-bg.webp') }}">
    <div class="container">
        <div class="footer-top padding-top padding-bottom">
            <div class="text-center mb-2">
                <a href="#0">
                    <img src="{{ asset('img/logoMenu.webp') }}" height="80px" alt="logo">
                </a>
            </div>
            <div class="mb-3">
                <p class="footer-link text-light">Email: vemitienda@gmail.com</p>
                <p class="footer-link text-light">Whatsapp: +58 0424 849 68 99</p>
            </div>
            <ul class="social-icons">
                <li>
                    <a target="_blank" href="https://www.facebook.com/profile.php?id=100090743526087"><i
                            class="fab fa-facebook-f"></i></a>
                </li>
                <li>
                    <a target="_blank" href="https://www.instagram.com/vemitienda.online"><i
                            class="fab fa-instagram"></i></a>
                </li>
            </ul>
        </div>
        <div class="footer-bottom">
            <ul class="footer-link">
                <li>
                    <a href="{{ url('/') }}#home">Inicio</a>
                </li>
                <li>
                    <a href="{{ url('/') }}#features">Características</a>
                </li>
                <li>
                    <a href="{{ url('/') }}#yourCustomers">Tus Clientes</a>
                </li>
                <li>
                    <a href="{{ url('/') }}#testimonies">Testimonios</a>
                </li>
                <li>
                    <a href="{{ url('/') }}/contacto">Contacto</a>
                </li>
                <li>
                    <a href="{{ url('/') }}/politica">Política de privacidad</a>
                </li>
                {{-- <li>
                    <a href="{{ url('/') }}/blog">Blog</a>
                </li> --}}
            </ul>
        </div>
        <div class="copyright">
            <p>
                Copyright © 2023. All Rights Reserved By <a href="#0">Ve mi Tienda</a>
            </p>
        </div>
    </div>
</footer>
