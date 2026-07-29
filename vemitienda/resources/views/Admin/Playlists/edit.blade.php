@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-list-music"></i> Editar playlist</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('playlists.show', $playlist->id) }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    {{ Session::get('errors') }}
    <div class="card-body">
        <form action="{{ route('playlists.update', $playlist->id) }}" method="POST">
            @csrf()
            <input type="hidden" name="_method" value="put">
            <div class="card-body">
                <div class="row">
                    <x-text name="name" columns="6" label="Nombre de la playlist" required="true"
                        placeholder="Ej: Ensayo banda" value="{{ old('name', $playlist->name) }}" />
                </div>
                <div class="row">
                    <x-textarea name="description" columns="8" label="Descripción (opcional)"
                        placeholder="Ingrese una descripción...">{{ old('description', $playlist->description) }}</x-textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
