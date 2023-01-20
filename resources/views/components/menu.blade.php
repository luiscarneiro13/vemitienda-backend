<div class="fixed-top bg-light mb-5">
    <div class="col-2"></div>
    <div class="col-8 d-flex flex-row align-middle">
        <div class="p-2">
            <img src="{{ $logo }}" width="60px" height="60px">
        </div>
        @if ($categories)
        <div class="dropdown mt-2">
            <button class="btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
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
    <div class="col-2"></div>
</div>
