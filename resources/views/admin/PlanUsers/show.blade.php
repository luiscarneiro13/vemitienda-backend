@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> Historial de planes del usuario</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('planusers.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>

    <!-- /.card-header -->
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered w-100 tabla-planusers">
            <thead>
                <tr>
                    @foreach ($data['nombreColumnas'] as $key=> $value)
                    <th>{{ @$key }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($data['infoData'] as $item)
                <tr>
                    @foreach ($data['nombreColumnas'] as $key=> $value)
                    <td>{{ @$item->$value }}</td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td class="text-center" colspan="{{ $data['nombreColumnas']->count() + 1 }}"">No hay datos disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
</div>
@endsection
