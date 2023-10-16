@extends('layouts.adminlte.index')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h5 class="text-default"><i class="fa fa-user-circle"></i> Crear Versión Android</h5>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('versions.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
                </div>
            </div>
        </div>
        {{ Session::get('errors') }}
        <div class="card-body">
            <form action="{{ route('versions.store') }}" method="POST">
                @csrf()
                <div class="card-body">
                    <div class="row">
                        <x-text name="version" columns="6" label="N° de versión" required="true" />
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn-sm btn btn-dark float-right"><i class="fa fa-save"></i>
                        Guardar</button>
                </div>
            </form>
        </div>

    </div>
@endsection
