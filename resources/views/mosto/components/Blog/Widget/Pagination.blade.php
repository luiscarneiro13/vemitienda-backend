<div class="pagination-area text-center pt-50 pb-50 pb-lg-none">
    @if ($data->previousPageUrl())
        <a href="#0"><i class="fas fa-angle-double-left"></i><span>Anterior</span></a>
    @endif

    @foreach ($data->getUrlRange(1, $data->total() / $data->perPage() + 1) as $key => $url)
        <a href="{{ $url }}" @if ($key == $data->currentPage()) class="active" @endif>{{ $key }}</a>
    @endforeach

    @if ($data->nextPageUrl())
        <a href="{{ $url }}"><span>Próximo</span><i class="fas fa-angle-double-right"></i></a>
    @endif
</div>
