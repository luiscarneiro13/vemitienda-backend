<section id="testimonies" class="testimonial-section padding-top-2">
    <div class="container">
        <div class="section-header">
            <h5 class="cate">Lo que dicen nuestros usuarios en la tienda de aplicaciones</h5>
            <h2 class="title">Testimonios</h2>
        </div>
        <div class="testimonial-wrapper">
            <a href="#0" class="testi-next trigger">
                <img src="{{ asset('img/client/left.webp') }}" alt="client">
            </a>
            <a href="#0" class="testi-prev trigger">
                <img src="{{ asset('img/client/right.webp') }}" alt="client">
            </a>
            <div class="testimonial-area testimonial-slider owl-carousel owl-theme">
                @forelse ($testimonies as $item)
                    @include('mosto.components.testimony', ['testimony' => $item])
                @empty
                    Nada
                @endforelse
            </div>
            <div class="button-area">
                {{-- <a href="#0" class="button-2">Leave a review <i class="flaticon-right"></i></a> --}}
            </div>
        </div>
    </div>
</section>
