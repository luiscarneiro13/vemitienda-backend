@foreach ($products as $product)
    <div class="col-lg-3 col-md-4 col-sm-6 mix oranges fresh-meat">
        <div class="featured__item">
            <div class="featured__item__pic set-bg"
                data-setbg="{{ count(@$product->image) > 0 ? env('DO_URL_BASE') . '/' . $product->image[0]->url : '' }}">
            </div>
            <div class="featured__item__text">
                <h6><a href="#">{{ $product->name }}</a></h6>
                @if ($product->price > 0)
                    <h5>${{ $product->price }}</h5>
                @endif
            </div>
        </div>
    </div>
@endforeach
