@extends('layouts.adminlte.index')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h5 class="text-default"><i class="fa fa-user-circle"></i> Editar Plan</h5>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('planusers.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('planusers.update', $planuser) }}" method="POST">
                @csrf()
                <input type="hidden" name="_method" value="put">
                <div class="card-body">
                    <div class="row">
                        <x-select columns=7 label="Usuario" required=true name="user_id" :datos="@$users"
                            :selected="@$planuser->user->id" />
                        <x-select columns=3 label="Plan" required=true name="plan_id" :datos="@$plans"
                            :selected="@$planuser->plan->id" />
                        <x-select columns=2 label="Activo" required=true name="activo" :datos="@$activo"
                            :selected="@$planuser->activo" />
                        <x-text columns="4" value="{{ @$planuser->start_date }}" label="Fecha Inicio" name="start_date" class="form-control"
                            id="start_date" />
                        <x-text columns="4" value="{{ @$planuser->end_date }}" label="Fecha Fin" name="end_date" class="form-control" id="end_date" />
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script>
        //Date picker
        $('#start_date').datepicker();
        $('#end_date').datepicker();
    </script>
@endsection
