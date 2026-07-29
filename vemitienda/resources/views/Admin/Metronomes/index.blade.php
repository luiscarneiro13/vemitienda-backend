@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-drum"></i> Mis metrónomos</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('metronomos.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i> Nuevo metrónomo</a>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Canción</th>
                    <th width="100">BPM</th>
                    <th width="70">Play</th>
                    <th width="140"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['infoData'] as $metronome)
                    <tr id="metronome-row-{{ $metronome->id }}">
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
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <a class="dropdown-toggle btn btn-sm btn-dark" data-toggle="dropdown">Opciones</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('metronomos.edit', $metronome->id) }}">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('metronomos.destroy', $metronome->id) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este metrónomo?')">
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
                        <td colspan="4" class="text-center">Todavía no has creado ningún metrónomo</td>
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

<x-metronome-player />
@endsection
