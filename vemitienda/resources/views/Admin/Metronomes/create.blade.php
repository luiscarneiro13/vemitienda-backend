@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-drum"></i> Nuevo metrónomo</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('metronomos.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    {{ Session::get('errors') }}
    <div class="card-body">
        <form action="{{ route('metronomos.store') }}" method="POST">
            @csrf()
            <div class="card-body">
                <div class="row">
                    <x-text name="title" columns="6" label="Título de la canción" required="true"
                        placeholder="Ej: Bohemian Rhapsody" value="{{ old('title') }}" />
                    <x-text name="artist" columns="6" label="Artista (opcional)"
                        placeholder="Ej: Queen" value="{{ old('artist') }}" />
                </div>
                <div class="row">
                    {{-- Input crudo (no <x-text>): ese componente fuerza value="0" para type=number
                         cuando el valor es vacío, lo que rompería el "dejar vacío = sin metrónomo". --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">BPM (opcional)</label>
                            <input id="bpm" name="bpm" type="number" min="20" max="300"
                                value="{{ old('bpm') }}" placeholder="120"
                                class="form-control @error('bpm') is-invalid @enderror">
                            @if (Session::has('errors') && Session::get('errors')->first('bpm'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ Session::get('errors')->first('bpm') }}</strong>
                                </span>
                            @endif
                            <span class="text-muted"><small>Dejalo vacío si esta canción no lleva metrónomo: solo quedará listada en la playlist.</small></span>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top:32px">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="var bpmVal = document.getElementById('bpm').value; if (!bpmVal) { toastr.warning('Ingresá un BPM para escuchar la vista previa'); } else { MetronomePlayer.load({id: 0, title: document.getElementById('title').value || 'Vista previa', artist: document.getElementById('artist').value, bpm: bpmVal}); MetronomePlayer.play(); }">
                            <i class="fa fa-headphones"></i> Escuchar BPM
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn-sm btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<x-metronome-player />
@endsection
