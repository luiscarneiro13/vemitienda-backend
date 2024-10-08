@foreach ($products as $product)
    <div class="col-lg-3 col-md-4 col-sm-6 mix oranges fresh-meat">
        <div class="featured__item">
            <div class="text-center">
                <image class="img-fluid"
                    src="{{ count(@$product->image) > 0 ? env('APP_URL') . '/' . $product->image[0]->url : '' }}" />
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


<div class="col-12">
    <div id="loader" style="display: none;" class="text-center">
        <div class="spinner-border" role="status">
            <span class="sr-only">Cargando más...</span>
        </div>
    </div>

</div>
