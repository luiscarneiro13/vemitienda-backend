<div class="fixed-top bg-light mb-5">
    <div class="col-2"></div>
    <div class="col-8 d-flex flex-row align-middle">
        <div class="p-2">
            <img src="{{ $logo }}" width="60px" height="60px">
        </div>
        @if ($categories)
        <div class="mt-2 text-center">
            <label for="">Ver por Categoría</label>
            <select id="categories" name="categories" class="form-select mb-3 target">
                <option value="">Seleccione una categoría</option>
                <option value="0">Todos los productos</option>
                @foreach (@$categories as $category)
                <option value="{{ $category->id }}">
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>
    <div class="col-2"></div>
    @if ((int)$cat>0)
        <h4 class="text-center">Categoría: {{ @$products[0]->category->name }}</h4>
    @endif
</div>
<script>
    $('#categories').change(function() {
    const idEncriptado='{{ $idEncriptado }}'
    const newUrl=`${window.location.origin}/share/${idEncriptado}?cat=${$('#categories').val()}`
    location=`${newUrl}`
});
</script>
