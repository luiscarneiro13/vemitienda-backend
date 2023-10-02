@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Editar Usuario</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('usuarios.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('usuarios.update',$usuario) }}" method="POST">
            @csrf()
            <input type="hidden" name="_method" value="put">
            <div class="card-body">
                <div class="row">
                    <x-text name="name" columns="6" label="Nombre Completo" required="true"
                        placeholder="Ingrese su nombre aquí..." value="{{ $usuario->name }}" />
                    <x-text name="email" columns="6" type="email" label="Correo electrónico" required="true"
                        placeholder="Ingrese su correo aquí..." value="{{ $usuario->email }}" />
                    <x-text name="password" columns="6" type="password" label="Contraseña"
                        small="Deje la contraseña en blanco si no desea cambiarla"
                        placeholder="Ingrese su contraseña aquí..." />
                    <x-text name="password" columns="6" type="password" label="Repita su contraseña"
                        placeholder="Ingrese su contraseña aquí..."
                        small="Deje la contraseña en blanco si no desea cambiarla" />
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
