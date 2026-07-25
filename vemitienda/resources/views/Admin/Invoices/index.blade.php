@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-file-invoice-dollar"></i> Facturas</h5>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive">
        <div class="float-right" style="margin-bottom:20px">
            <form class="d-flex align-items-center" style="gap:8px; white-space:nowrap">
                <label class="d-flex align-items-center mb-0" style="gap:4px">
                    Método de pago:
                    <select name="payment_method" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach ($data['paymentMethods'] as $method)
                            <option value="{{ $method }}" {{ request()->get('payment_method') === $method ? 'selected' : '' }}>
                                {{ $method }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="d-flex align-items-center mb-0" style="gap:4px">
                    Buscar:
                    <input type="search" name="query" class="form-control form-control-sm"
                        value="{{ @request()->get('query') }}">
                </label>
                <button class="btn btn-dark btn-xs"><i class="fa fa-search"></i></button>
            </form>
        </div>

        <table class="table table-striped table-bordered table-sm w-100" style="font-size:12px">
            <thead>
                <tr>
                    <th width="60"></th>
                    <th>Fecha</th>
                    <th>ID Transacción</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Monto enviado</th>
                    <th>Monto recibido</th>
                    <th>Peer</th>
                    <th>Método de pago</th>
                    <th>Comisión</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['infoData'] as $item)
                    <tr>
                        <td class="text-center">
                            <a href="{{ route('facturas.show', $item['Transaction ID']) }}" class="text-dark" title="Ver detalle"><i class="fa fa-eye"></i></a>
                            &nbsp;
                            <a href="#" class="text-danger" title="Descargar PDF"><i class="fa fa-file-pdf"></i></a>
                        </td>
                        <td>{{ $item['Created At (UTC)'] ?? '' }}</td>
                        <td>{{ $item['Transaction ID'] ?? '' }}</td>
                        <td>{{ $item['Type'] ?? '' }}</td>
                        <td>
                            @php $estado = $item['Status'] ?? ''; @endphp
                            <span class="badge {{ $estado === 'Completada' ? 'badge-success' : 'badge-secondary' }}">
                                {{ $estado }}
                            </span>
                        </td>
                        <td>{{ $item['Funds To Send Amount'] ?? '' }} {{ $item['Funds To Send Currency'] ?? '' }}</td>
                        <td>{{ $item['Funds To Receive Amount'] ?? '' }} {{ $item['Funds To Receive Currency'] ?? '' }}</td>
                        <td>{{ $item['Peer'] ?? '' }}</td>
                        <td>{{ $item['Payment Method'] ?? '' }}</td>
                        <td>{{ $item['Service Fee'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No hay facturas disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px">
            <div class="float-left" style="margin-top:10px">
                Mostrando {{ $data['infoData']->perPage() }} de {{ $data['infoData']->total() }} registros
            </div>
            <div class="float-right">
                {{ $data['infoData']->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
