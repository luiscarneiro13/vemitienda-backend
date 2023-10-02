@extends('layouts.adminlte.index')
@section('content')
<div class="card card-secondary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h6 class="text-white">Ayuda</h6>
            </div>
            <div class="col-6 text-right">
            </div>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive">
        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <div class="row">
                <div class="col-sm-12">
                    <table id="ayuda-table" class="table table-bordered table-striped dataTable dtr-inline tabla-default" role="grid"
                        aria-describedby="datatable_info">
                        <thead>
                            <tr role="row">
                                <th>Nombre</th>
                                <th>Comando</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        @forelse ($ayuda as $item)
                        <tr>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->comando }}</td>
                            <td>{{ $item->descripcion }}</td>
                        </tr>
                        @empty
                        <tr class="text-center">No hay usuarios registrados</tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
