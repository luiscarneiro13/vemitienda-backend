@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Editar Plan</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('plans.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('plans.update',$plan) }}" method="POST">
            @csrf()
            <input type="hidden" name="_method" value="put">
            <div class="card-body">
                <div class="row">
                    <x-text name="name" columns="6" label="Nombre del Plan" required="true"
                        placeholder="Ingrese su plan aquí..." value="{{ $plan->name }}" />
                    <x-text type="number" name="quantity" columns="6" label="Cantidad de Productos" required="true"
                        placeholder="Ingrese la cantidad de productos aquí..." value="{{ $plan->quantity }}" />

                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
