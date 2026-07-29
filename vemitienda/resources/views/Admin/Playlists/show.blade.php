@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-list-music"></i> {{ $playlist->name }}</h5>
                @if ($playlist->description)
                    <small class="text-muted">{{ $playlist->description }}</small>
                @endif
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('playlists.edit', $playlist->id) }}" class="btn btn-dark btn-xs"><i class="fa fa-edit"></i> Editar playlist</a>
                <a href="{{ route('playlists.index') }}" class="btn btn-dark btn-xs">Volver</a>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive">
        @if ($playlist->metronomes->count() > 1)
            <p class="text-muted"><small><i class="fa fa-arrows-alt"></i> Arrastrá las canciones desde el ícono <i class="fa fa-grip-vertical"></i> para reordenar la playlist.</small></p>
        @endif
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th width="30"></th>
                    <th>Canción</th>
                    <th width="100">BPM</th>
                    <th width="70">Play</th>
                    <th width="100"></th>
                </tr>
            </thead>
            <tbody id="playlist-songs-body">
                @forelse ($playlist->metronomes as $metronome)
                    <tr id="metronome-row-{{ $metronome->id }}" data-metronome-id="{{ $metronome->id }}">
                        <td class="text-center drag-handle" style="cursor:move">
                            <i class="fa fa-grip-vertical text-muted"></i>
                        </td>
                        <td>
                            {{ $metronome->title }}
                            @if ($metronome->artist)
                                <br><small class="text-muted">{{ $metronome->artist }}</small>
                            @endif
                        </td>
                        <td>{{ $metronome->bpm }}</td>
                        <td class="text-center">
                            @php
                                $metronomeData = ['id' => $metronome->id, 'title' => $metronome->title, 'artist' => $metronome->artist, 'bpm' => $metronome->bpm];
                            @endphp
                            <button type="button" class="btn btn-sm btn-primary" title="Reproducir"
                                onclick='MetronomePlayer.load(@json($metronomeData)); MetronomePlayer.play();'>
                                <i class="fa fa-play"></i>
                            </button>
                        </td>
                        <td>
                            <form action="{{ route('playlists.metronomos.detach', ['playlist' => $playlist->id, 'metronome' => $metronome->id]) }}" method="POST"
                                onsubmit="return confirm('¿Quitar esta canción de la playlist?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Quitar de la playlist">
                                    <i class="fa fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Esta playlist todavía no tiene canciones</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        @if ($availableMetronomes->isEmpty())
            <span class="text-muted">
                No tienes más metrónomos disponibles para agregar.
                <a href="{{ route('metronomos.create') }}">Crea uno nuevo</a>.
            </span>
        @else
            <form action="{{ route('playlists.metronomos.attach', ['playlist' => $playlist->id, 'metronome' => '__ID__']) }}"
                method="POST" id="attach-metronome-form" class="form-inline"
                onsubmit="this.action = this.action.replace('__ID__', document.getElementById('attach-metronome-select').value); return document.getElementById('attach-metronome-select').value !== '';">
                @csrf
                <select id="attach-metronome-select" class="form-control mr-2" style="min-width:260px">
                    @foreach ($availableMetronomes as $metronome)
                        <option value="{{ $metronome->id }}">{{ $metronome->title }} ({{ $metronome->bpm }} BPM)</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-dark"><i class="fa fa-plus-circle"></i> Agregar a la playlist</button>
            </form>
        @endif
    </div>
</div>

<x-metronome-player />
@endsection

@section('js')
<script>
    // Reordenamiento drag & drop de las canciones de la playlist con jQuery UI Sortable
    // (ya viene cargado globalmente en el layout admin, no requiere ninguna librería nueva).
    $(function () {
        $('#playlist-songs-body').sortable({
            handle: '.drag-handle',
            axis: 'y',
            cursor: 'move',
            placeholder: 'table-active',
            helper: function (e, tr) {
                // Mantiene el ancho de cada celda mientras se arrastra la fila.
                var originals = tr.children();
                var helper = tr.clone();
                helper.children().each(function (index) {
                    $(this).width(originals.eq(index).width());
                });
                return helper;
            },
            update: function () {
                var order = $('#playlist-songs-body tr').map(function () {
                    return $(this).data('metronome-id');
                }).get();

                $.ajax({
                    url: '{{ route('playlists.reorder', $playlist->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order,
                    },
                }).fail(function () {
                    toastr.error('No se pudo guardar el nuevo orden de la playlist');
                });
            },
        });
    });
</script>
@endsection
