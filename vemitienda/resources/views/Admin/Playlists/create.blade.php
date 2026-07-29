@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-list-music"></i> Nueva playlist</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('playlists.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    {{ Session::get('errors') }}
    <div class="card-body">
        <form action="{{ route('playlists.store') }}" method="POST">
            @csrf()
            <div class="card-body">
                <div class="row">
                    <x-text name="name" columns="6" label="Nombre de la playlist" required="true"
                        placeholder="Ej: Ensayo banda" value="{{ old('name') }}" />
                </div>
                <div class="row">
                    <x-textarea name="description" columns="8" label="Descripción (opcional)"
                        placeholder="Ingrese una descripción...">{{ old('description') }}</x-textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn-sm btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
