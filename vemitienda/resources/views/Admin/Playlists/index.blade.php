@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-list-music"></i> Mis playlists</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('playlists.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i> Nueva playlist</a>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th width="140">Cantidad canciones</th>
                    <th width="180"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['infoData'] as $playlist)
                    <tr>
                        <td>
                            {{ $playlist->name }}
                            @if ($playlist->description)
                                <br><small class="text-muted">{{ $playlist->description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $playlist->metronomes_count }}</td>
                        <td>
                            <a href="{{ route('playlists.show', $playlist->id) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-folder-open"></i> Abrir
                            </a>
                            <a href="{{ route('playlist.public.show', $playlist->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="Ver vista pública">
                                <i class="fa fa-external-link-alt"></i> Enlace público
                            </a>
                            <div class="input-group" style="display:inline-block">
                                <div class="input-group-prepend" style="display:inline-block">
                                    <a class="dropdown-toggle btn btn-sm btn-dark" data-toggle="dropdown">Opciones</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('playlists.edit', $playlist->id) }}">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar esta playlist?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Todavía no has creado ninguna playlist</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px">
            <div class="float-left" style="margin-top:10px">
                Mostrando {{ $data['infoData']->perPage() }} de {{ $data['infoData']->total() }} registros
            </div>
            <div class="float-right">
                {{ $data['infoData']->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
