<div class="pagination-area text-center pt-50 pb-50 pb-lg-none">
    @if ($data->previousPageUrl())
        <a href="{{ $data->previousPageUrl() }}"><i class="fas fa-angle-double-left"></i><span>Anterior</span></a>
    @endif

    @if ($data->total() > 9)
        @foreach ($data->getUrlRange(1, $data->total() / $data->perPage() + 1) as $key => $url)
            <a href="{{ $url }}" @if ($key == $data->currentPage()) class="active" @endif>{{ $key }}</a>
        @endforeach
    @endif

    @if ($data->nextPageUrl())
        <a href="{{ $data->nextPageUrl() }}"><span>Próximo</span><i class="fas fa-angle-double-right"></i></a>
    @endif
</div>
