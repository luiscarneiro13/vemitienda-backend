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
                    <x-text name="bpm" type="number" min="20" max="300" columns="4" label="BPM" required="true"
                        placeholder="120" value="{{ old('bpm', 120) }}" />
                    <div class="col-md-3" style="margin-top:32px">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="MetronomePlayer.load({id: 0, title: document.getElementById('title').value || 'Vista previa', artist: document.getElementById('artist').value, bpm: document.getElementById('bpm').value}); MetronomePlayer.play();">
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
