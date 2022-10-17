@extends('layouts.adminlte.acceso')

@section('content')

<form method="POST" action="">
    @csrf
    <div class="row">
        <x-text name="email" columns="12" label="Email" placeholder="Ingrese su email aquí..." />
    </div>
    <div class="row">
        <x-text type="password" name="password" columns="12" label="Contraseña"
            placeholder="Ingrese su contraseña aquí..." />
    </div>
    <div class="row mt-3">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-small btn-dark">
                <i class="fa fa-unlock"></i> Iniciar Sesión
            </button>
        </div>
    </div>
</form>
<div class="row mt-5">
    <div class="col-6 text-center">
        <a href="#" class="btn-sm btn-default text-primary">
            Recuperar Cuenta
        </a>
    </div>
    <div class="col-6 text-center">
        <a href="#" class="btn-sm btn-default text-primary">
            Registro
        </a>
    </div>
</div>
<div class="row mt-1">
    <div class="col-12 text-right">
        Versión 1.0.0
    </div>
</div>
@endsection
