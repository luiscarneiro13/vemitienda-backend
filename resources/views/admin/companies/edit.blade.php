@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default">Editar Empresa</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('companies.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('companies.update',$company) }}" method="POST">
            @csrf()
            <input type="hidden" name="_method" value="put">
            <div class="card-body">
                <div class="row">
                    <x-text value="{{ $company->name }}" name="name" columns="6" label="Nombre de la Empresa" required="true"
                        placeholder="Ingrese el nombre de la empresa aquí..." />
                    
                    <x-text value="{{ $company->slogan }}" name="slogan" columns="6" label="Slogan de la Empresa" required="true"
                        placeholder="Ingrese el slogan de la empresa aquí..." />
                    
                    <x-text value="{{ $company->email }}" name="email" columns="6" label="Email de la Empresa" required="true"
                        placeholder="Ingrese el email de la empresa aquí..." />
                    
                    <x-text value="{{ $company->phone }}" name="phone" columns="6" label="Teléfono de la Empresa" required="true"
                        placeholder="Ingrese el teléfono de la empresa aquí..." />
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection