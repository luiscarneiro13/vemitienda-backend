@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Crear Usuario</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('users.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf()
            <div class="card-body">
                <div class="row">
                    <x-text name="name" columns="6" label="Nombre Completo" required="true"
                        placeholder="Ingrese su nombre aquí..." />
                    <x-text name="email" columns="6" type="email" label="Correo electrónico" required="true"
                        placeholder="Ingrese su correo aquí..." />
                    <x-text name="password" columns="6" type="password" label="Contraseña" required="true"
                        placeholder="Ingrese su contraseña aquí..." />
                    <x-text name="password" columns="6" type="password" label="Repita la contraseña" required="true"
                        placeholder="Ingrese su contraseña aquí..." />
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>

</div>
@endsection