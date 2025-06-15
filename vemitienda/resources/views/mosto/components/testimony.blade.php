<div class="testimonial-item">
    <div class="testimonial-content">
        <div class="ratings">
            @for ($i = 1; $i <= (int) $testimony->rate; $i++)
                <span><i class="fas fa-star"></i></span>
            @endfor

        </div>
        <p>{{ $testimony->description }}</p>
        <h5 class="title"><a href="#0">{{ $testimony->user }}</a></h5>
    </div>
</div>
