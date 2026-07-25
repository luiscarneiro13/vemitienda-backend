@extends('layouts.adminlte.index')
@section('content')

@php
    $statusBadge = [
        'draft' => 'badge-secondary',
        'sent' => 'badge-info',
        'paid' => 'badge-success',
        'cancelled' => 'badge-danger',
    ];
@endphp

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-file-invoice-dollar"></i> Facturas</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('facturas.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i> Nueva factura</a>
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
                    <th width="90"></th>
                    <th>Fecha</th>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Método de pago</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['infoData'] as $invoice)
                    <tr>
                        <td class="text-center">
                            <a href="{{ route('facturas.show', $invoice->number) }}" class="text-dark" title="Ver detalle"><i class="fa fa-eye"></i></a>
                            &nbsp;
                            <a href="{{ route('facturas.edit', $invoice->number) }}" class="text-dark" title="Editar"><i class="fa fa-edit"></i></a>
                            &nbsp;
                            <a href="{{ route('facturas.pdf', $invoice->number) }}" class="text-danger" title="Descargar PDF"><i class="fa fa-file-pdf"></i></a>
                        </td>
                        <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
                        <td>{{ $invoice->number }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>
                            <span class="badge {{ $statusBadge[$invoice->status] ?? 'badge-secondary' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td>{{ $invoice->payment_method ?? 'N/A' }}</td>
                        <td>${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay facturas disponibles</td>
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
