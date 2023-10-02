@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Crear Usuario</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('usuarios.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
{{ Session::get('errors') }}
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf()
            <div class="card-body">
                <div class="row">
                    <x-text value="Luis Carneiro" name="name" columns="6" label="Nombre Completo" required="true"
                        placeholder="Ingrese su nombre aquí..." />
                    <x-text value="carneiroluis2@gmail.com" name="email" columns="6" type="email" label="Correo electrónico" required="true"
                        placeholder="Ingrese su correo aquí..." />
                    <x-text value="123456" name="password" columns="6" type="password" label="Contraseña" required="true"
                        placeholder="Ingrese su contraseña aquí..." />
                    <x-text name="password_confirmation" columns="6" type="password" label="Repita su contraseña" required="true"
                        placeholder="Ingrese su contraseña aquí..." />
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn-sm btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>

</div>
@endsection
