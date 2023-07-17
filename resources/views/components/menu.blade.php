<div class="fixed-top bg-light mb-5">
    <div class="col-2"></div>
    <div class="col-8 d-flex flex-row align-middle">
        <div class="p-2">
            <img src="{{ $logo }}" width="60px" height="60px">
        </div>
        @if ($categories)
            <div class="mt-2 text-center">
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
        <br>
        <input style="width: 200px;height:40px;margin-left:20px" type="text" name="query" id="query"
            placeholder="Filtrar por nombre" class="mt-2 form-control">
        <button id="submit" style="height:40px;margin-left:5px" class="mt-2 btn btn-sm btn-primary">
            <i class="fas fa-search"></i>
        </button>
        {{-- <div class="row mt-2 text-center">
            <label for="">Ver por Categoría</label>

        </div> --}}
    </div>
    <div class="col-2"></div>
    @if ((int) $cat > 0)
        <h4 class="text-center">Categoría: {{ @$products[0]->category->name }}</h4>
    @endif
</div>
<script>
    $('#categories').change(function() {
        const idEncriptado = '{{ $idEncriptado }}'
        const newUrl = `${window.location.origin}/share/${idEncriptado}?cat=${$('#categories').val()}`
        location = `${newUrl}`
    });
    $('#submit').click(function() {
        const idEncriptado = '{{ $idEncriptado }}'
        location = `${window.location.origin}/share/${idEncriptado}?query=${$('#query').val()}`
    })
</script>
