@extends('layouts.adminlte.index')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h5 class="text-default"><i class="fa fa-user-circle"></i> Editar Etiqueta</h5>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('tags.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('tags.update', $tag) }}" method="POST">
                @csrf()
                <input type="hidden" name="_method" value="put">
                <div class="card-body">
                    <div class="row">
                        <x-text name="name" columns="6" label="Nombre de la etiqueta" required="true"
                            placeholder="Ingrese su etiqueta aquí..." value="{{ $tag->name }}" />
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
