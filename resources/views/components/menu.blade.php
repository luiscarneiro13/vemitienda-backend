<div class="row fixed-top bg-light">
    <div class="col-md-4"></div>
    <div class="col-2">
        <div class="text-center p-2">
            <img src="{{ $logo }}" width="40px" height="40px">
        </div>
    </div>
    <div class="col-2">
        @if ($categories)
            <div class="dropdown mt-2 text-center">
                <button class="btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Categorías
                </button>
                <ul class="dropdown-menu">
                    @foreach (@$categories as $category)
                    <li><a href="#{{ $category->name }}" class="dropdown-item">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="col-md-4"></div>
</div>
