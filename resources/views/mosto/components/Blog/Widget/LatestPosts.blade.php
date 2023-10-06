<div class="widget widget-post">
    <h5 class="title">latest post</h5>
    <div class="slider-nav">
        <span class="widget-prev"><i class="fas fa-angle-left"></i></span>
        <span class="widget-next"><i class="fas fa-angle-right"></i></span>
    </div>
    <div class="widget-slider owl-carousel owl-theme">
        @forelse ($latests as $latest)
            <div class="item">
                <div class="thumb">
                    {{-- <a href="#0">
                    <img src="./assets/images/blog/slider01.png" alt="blog">
                </a> --}}
                </div>
                <div class="content">
                    <h6 class="p-title">
                        <a href="{{ url('blog') . '/' . $latest->slug }}">{{ $latest->name }}</a>
                    </h6>
                    <div class="meta-post">
                        {{-- <a href="#0" class="mr-4"><i class="flaticon-chat-1"></i>20
                        Comments</a>
                    <a href="#0"><i class="flaticon-eye"></i>466 View</a> --}}
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>
</div>
