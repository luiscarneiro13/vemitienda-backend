@extends('layouts.adminlte.index')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h5 class="text-default"><i class="fa fa-user-circle"></i> Pagos</h5>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('payments.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i>
                        Agregar</a>
                </div>
            </div>
        </div>

        <!-- /.card-header -->
        <div class="card-body table-responsive">
            <div class="form-inline float-right" style="margin-bottom:20px">
                <form>
                    <label>
                        Buscar:&nbsp;
                        <input type="search" name="query" class="form-control form-control-sm"
                            value="{{ @request()->get('query') }}">
                        <button class="btn btn-dark btn-xs ml-2"><i class="fa fa-search"></i></button>
                    </label>
                </form>
            </div>
            <table class="table table-striped table-bordered w-100 tabla-payments">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Pagado</th>
                        <th width="15%"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['infoData'] as $item)
                        <tr>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->inicio }}</td>
                            <td>{{ $item->fin }}</td>
                            <td>
                                @if (@$item->pagado == 'Pagado')
                                    <span class="badge bg-primary">Pagado</span>
                                @else
                                    <span class="badge bg-warning">Pendiente</span>
                                @endif
                            </td>

                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a class="dropdown-toggle btn btn-sm btn-dark" data-toggle="dropdown"
                                            aria-expanded="false">
                                            Opciones
                                        </a>
                                        <div class="dropdown-menu" style="">
                                            <form class="formEliminar" data-nombre="{{ $item->label }}"
                                                action="{{ route('payments.destroy', $item->id) }}" method="post">
                                                <button class="dropdown-item" type="submit"><i class="fa fa-trash"></i>
                                                    Eliminar</button>
                                                <input type="hidden" name="_method" value="delete" />
                                                <input type="hidden" name="_token" value="{{ $data['token'] }}">
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6">No hay datos disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if (!request()->get('query'))
                <div style=" margin-top: 20px">
                    <div class="float-left" style="margin-top: 10px">
                        Mostrando {{ $data['infoData']->perPage() }} registros de un total de
                        {{ $data['infoData']->total() }}
                    </div>
                    <div class="float-right table-responsive">
                        {{ $data['infoData']->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
