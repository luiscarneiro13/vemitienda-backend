@extends('layouts.adminlte.index')
@section('content')
<div class="row">
    <x-widget icon="far fa-user" title="Usuarios Registrados" value="{{ $usuarios_registrados }}"/>
    <x-widget icon="far fa-credit-card" title="Usuarios de Pago" value="10"/>
    <x-widget icon="nav-icon fa fa-coins" title="Cobrar este Mes" value="10"/>
    <x-widget icon="nav-icon fa fa-coins" title="Total Cobrado" value="10"/>
</div>

<center><img class="text-center" width="300px" height="300px" src="{{ asset('img/logo.png') }}" alt=""></center>

@endsection

{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}