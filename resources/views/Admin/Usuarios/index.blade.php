@extends('layouts.adminlte.index')
@section('content')
    <h3>Totales</h3>
    <div class="row">

        <div class="col-md-3">
            <x-cardDashboard title="Usuarios totales" number="{{ $usuariosTotales }}" />
        </div>

        <div class="col-md-3">
            <x-cardDashboard title="Productos totales" number="{{ $productosTotales }}" />
        </div>

        <div class="col-md-3">
            <x-cardDashboard title="Visitas" number="{{ $visits }}" />
        </div>

    </div>

    <hr />

    <h3>Reales</h3>
    <div class="row">
        <div class="col-md-3">
            <x-cardDashboard title="Total usuarios" number="{{ $totalUsuariosReales }}" />
        </div>

        <div class="col-md-3">
            <x-cardDashboard title="Total productos" number="{{ $totalProductosReales }}" />
        </div>

        <div class="col-md-3">
            <x-cardDashboard title="Usuarios con productos" number="{{ $usuariosRealesConProductos }}" />
        </div>
    </div>










    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h5 class="text-default"><i class="fa fa-user-circle"></i> Usuarios</h5>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i>
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
            <table class="table table-striped table-bordered w-100 tabla-usuarios">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>N° Prod</th>
                        <th>Tienda</th>
                        <th>Links</th>
                        <th width="15%"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['infoData'] as $item)
                        <tr>
                            <td>{{ $item->email }}</td>
                            <td>{{ @count($item->products) }}</td>
                            <td>{{ $item->company->name ?? '' }}</td>
                            <td>
                                @if (@$item->company->url_catalogo)
                                    <a target="_blank" href="{{ $item->company->url_catalogo }}">Catálogo</a>
                                    -
                                    <a target="_blank" href="{{ $item->company->url_tienda_new }}">Tienda</a>
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

                                            @if (@$edit)
                                                <a class="dropdown-item" href="{{ route('usuarios.edit', $item->id) }}"><i
                                                        class="fa fa-edit"></i>
                                                    Editar</a>


                                                <a class="dropdown-item" href="{{ route('usuarios.edit', $item->id) }}"><i
                                                        class="fa fa-eye"></i>
                                                    Ver datos</a>
                                            @endif

                                            <div class="dropdown-divider"></div>

                                            <form class="formEliminar" data-nombre="{{ $item->label }}"
                                                action="{{ route('usuarios.destroy', $item->id) }}" method="post">
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
                            <td class="text-center" colspan="{{ $data['nombreColumnas']->count() + 1 }}"">No hay datos
                                disponibles</td>
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


    {{-- <x-Grilla :data="@$data" resource='usuarios' edit="true" /> --}}
@endsection
