<article class="mb-40-none mb-3">
    @if (isset($detail))
        <div class="post-item">
            <div class="post-thumb">
                @if (isset($blog->image->url))
                    <img src="{{ asset($blog->image->url) }}" alt="blog">
                @else
                    <img src="{{ asset('img/page-header1.webp') }}" alt="blog">
                @endif
            </div>
            <div class="post-content">
                <img src="{{ asset($image) }}" alt="" class="img-fluid">
                <h3 class="title mt-5">
                    <a href="{{ @$url }}">{{ @$name }}</a>
                </h3>
                {!! str_replace('<img', '<img class="img-responsive"', $body) !!}
            </div>
        </div>
    @else
        <div class="post-item">
            <div class="post-thumb">
                <img src="{{ asset('img/page-header1.webp') }}" alt="blog">
            </div>
            <div class="post-content">
                <h3 class="title">
                    <a href="{{ @$url }}">{{ @$name }}</a>
                </h3>
                <p>{{ @$extract }}</p>
                <div class="widget-search">
                    <button type="submit"><a href="{{ @$url }}" style="color:#504c89">Ver más</a></button>
                </div>
            </div>
        </div>
    @endif
</article>
