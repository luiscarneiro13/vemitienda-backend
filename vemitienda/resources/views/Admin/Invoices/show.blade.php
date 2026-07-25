@extends('layouts.adminlte.index')
@section('content')

@php
    $invoice = $data['item'];
    $money = fn($v) => '$' . number_format((float) $v, 2);
@endphp

<div class="mb-3">
    <a href="{{ route('facturas.index') }}" class="btn btn-dark btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
    <a href="{{ route('facturas.edit', $invoice->number) }}" class="btn btn-outline-dark btn-xs"><i class="fa fa-edit"></i> Editar</a>
    <button type="button" class="btn btn-outline-secondary btn-xs" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button>
</div>

<div class="invoice-paper mx-auto">

    <div class="row no-gutters align-items-center">
        <div class="col-6">
            <div class="d-flex align-items-center">
                <img src="{{ asset('img/web-design-cropped.png') }}" alt="Logo" style="max-width:220px;max-height:60px;object-fit:contain;object-position:left">
            </div>
        </div>
        <div class="col-6 text-right">
            <div class="font-weight-bold" style="font-size:32px; letter-spacing:2px">INVOICE</div>
        </div>
    </div>

    <div class="mb-4">
        <div class="font-weight-bold" style="font-size:14px">Luis Carneiro</div>
    </div>

    <div class="row no-gutters mb-4">
        <div class="col-6">
            <div class="text-muted" style="font-size:11px; text-transform:uppercase">Invoice to:</div>
            <div class="font-weight-bold" style="font-size:14px">{{ $invoice->customer_name }}</div>
        </div>
        <div class="col-6 text-right">
            <table class="ml-auto" style="font-size:12px">
                <tr>
                    <td class="text-muted pr-3">Invoice#</td>
                    <td class="font-weight-bold">{{ $invoice->number }}</td>
                </tr>
                <tr>
                    <td class="text-muted pr-3">Date</td>
                    <td class="font-weight-bold">{{ $invoice->issue_date->format('Y-m-d') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="table table-sm table-bordered invoice-items-table" style="font-size:12px; margin-bottom:60px">
        <thead class="thead-light">
            <tr>
                <th width="40">SL.</th>
                <th>Item Description</th>
                <th width="120">Price</th>
                <th width="60">Qty.</th>
                <th width="120">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->items as $line)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $line['description'] ?? '' }}</td>
                    <td>{{ $money($line['price'] ?? 0) }}</td>
                    <td>{{ $line['quantity'] ?? 1 }}</td>
                    <td>{{ $money($line['total'] ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sin ítems</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="row no-gutters">
        <div class="col-6">
            <div class="font-weight-bold mb-1" style="font-size:12px">Payment Info:</div>
            <table style="font-size:12px">
                <tr>
                    <td class="text-muted pr-3">Method:</td>
                    <td>{{ $invoice->payment_method ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <table class="ml-auto" style="font-size:12px; width:60%">
                <tr>
                    <td class="text-muted">Sub Total:</td>
                    <td class="text-right">{{ $money($invoice->subtotal) }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Tax:</td>
                    <td class="text-right">{{ $money($invoice->tax) }}</td>
                </tr>
                <tr style="border-top:2px solid #333">
                    <td class="font-weight-bold pt-2">Total:</td>
                    <td class="text-right font-weight-bold pt-2">{{ $money($invoice->total) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <div class="font-weight-bold mb-1" style="font-size:12px">Terms & Conditions</div>
        <div style="font-size:12px">{{ $invoice->terms_and_conditions ?: 'Once payment has been made, the customer is entitled to up to 2 inspections' }}</div>
    </div>

    <div class="row no-gutters mt-5">
        <div class="col-6"></div>
        <div class="col-6 text-center">
            <img src="{{ asset('img/firma-cropped.png') }}" alt="Firma" style="max-width:40%;max-height:35px;object-fit:contain">
            <div style="border-top:1px solid #333; width:70%; margin:0 auto; padding-top:4px; font-size:11px" class="text-muted">
                Luis Carneiro
            </div>
        </div>
    </div>

</div>

<style>
    .invoice-paper {
        max-width: 800px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 40px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .invoice-paper table td { padding: 2px 0; }
    .invoice-items-table th,
    .invoice-items-table td {
        text-align: center;
        vertical-align: middle;
        padding: 10px !important;
    }
    @media print {
        #app-sidebar, nav, .btn { display: none !important; }
        .invoice-paper { border: none; box-shadow: none; }
    }
</style>

@endsection
