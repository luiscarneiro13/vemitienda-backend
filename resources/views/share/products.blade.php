@if(@$products)
<div class="row contenedor">
    <div id="products">
        @foreach ($products as $product)
        <div class="col-md-4 p-1">
            <div class="text-center">
                <image class="img-fluid"
                    src="{{ count(@$product->image)>0 ? env('DO_URL_BASE').'/'.$product->image[0]->url:'' }}" />
            </div>
            <p class="text-center">
                <span style="font-weight: bold; font-size: 14px">{{ $product->name }}</span><br />
                @if ($product->price>0)
                <span style="font-weight: bold; font-size: 16px">${{ $product->price }}</span><br />
                @endif
            </p>
        </div>
        @endforeach
        <div class="d-none">
            {{ $products->links() }}
        </div>
        <div class="loading"></div>
    </div>
</div>
@else
<div class="text-center">
    <p>
        No se puede mostrar el catálogo ahora
    </p>
</div>
@endif
