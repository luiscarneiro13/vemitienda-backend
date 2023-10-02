@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Editar Servicio</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('services.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('services.update',$service) }}" method="POST">
            @csrf()
            <input type="hidden" name="_method" value="put">
            <div class="card-body">
                <div class="row">
                    <x-text name="name" columns="6" label="Nombre del Servicio" required="true"
                        placeholder="Ingrese su nombre de servicio aquí..." value="{{ $service->name }}" />
                    <x-text name="quantity" type="number" columns="6" label="Cantidad" required="true"
                        placeholder="Ingrese la cantidad de servicio aquí..." value="{{ $service->quantity }}" />
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
