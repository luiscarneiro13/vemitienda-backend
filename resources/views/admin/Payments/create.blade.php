@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Crear Pago de Plan</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('payments.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    {{ Session::get('errors') }}
    <div class="card-body">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf()
            <div class="card-body">
                <div class="row">
                    <x-select class="select2" columns=6 label="Usuario" required=true name="user_id" :datos="@$users" />
                    <x-select columns=3 label="Plan contratado" required=true name="plan_id" :datos="@$plans" />
                    <x-select columns=3 label="Método de Pago" required=true name="payment_method_id" :datos="@$paymentmethods" />
                    <x-select columns=3 label="Cantidad de meses a pagar" required=true name="months" :datos="@$months" />
                    <x-text columns=3 label="Cantidad recibida" required=true name="price" small="Ej: 100,00" />
                    <x-text columns=6 label="Nro de Transacción" required=true name="reference_number" />
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
@section('js')
    <script>
        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-3d'
        });
    </script>
@endsection
