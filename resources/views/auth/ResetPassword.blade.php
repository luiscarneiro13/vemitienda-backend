@extends('layouts.adminlte.acceso')

@section('content')
<form method="POST" action="{{ route('reset') }}">
    @csrf
    <div class="row">
        <x-text type="hidden" name="user_id" value="{{ $user_id }}" />
        <x-text type="password" name="password" columns="12" label="Contraseña"
            placeholder="Ingrese su contraseña aquí..." />
        <x-text type="password" name="password_confirmation" columns="12" label="Repita la Contraseña"
            placeholder="Ingrese su contraseña aquí..." />
    </div>
    <div class="row mt-3">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-small btn-primary">
                <i class="fa fa-unlock"></i> Restablecer
            </button>
        </div>
    </div>
</form>
@endsection
