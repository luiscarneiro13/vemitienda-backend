<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $invoice->number }}</title>
    <style>
        @php
            $money = fn($v) => '$' . number_format((float) $v, 2);
        @endphp

        @page {
            margin: 20px 40px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #222;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .invoice-paper {
            width: 100%;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        .col-left {
            float: left;
            width: 50%;
        }

        .col-right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #777;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .header-table td {
            vertical-align: middle;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
            text-align: right;
        }

        .issuer-name {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 20px 0;
        }

        .invoice-to-label {
            color: #777;
            font-size: 11px;
            text-transform: uppercase;
        }

        .invoice-to-name {
            font-size: 14px;
            font-weight: bold;
        }

        .meta-table {
            font-size: 12px;
        }

        .meta-table td {
            padding: 2px 0;
        }

        .meta-table td.label {
            color: #777;
            padding-right: 12px;
        }

        .items-table {
            width: 100%;
            border: 1px solid #ccc;
            margin: 20px 0 40px 0;
            font-size: 12px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .items-table thead th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .payment-info-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .totals-table {
            font-size: 12px;
            width: 60%;
            float: right;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .totals-table td.label {
            color: #777;
        }

        .totals-table tr.total-row td {
            border-top: 2px solid #333;
            font-weight: bold;
            padding-top: 8px;
        }

        .terms-section {
            margin-top: 20px;
            clear: both;
        }

        .terms-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .terms-text {
            font-size: 12px;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-box {
            width: 40%;
            float: right;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 70%;
            margin: 4px auto 0 auto;
            padding-top: 4px;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="invoice-paper">

        <table class="header-table">
            <tr>
                <td style="width:50%">
                    <img src="{{ public_path('img/web-design-cropped.png') }}" alt="Logo" style="max-width:220px;max-height:60px;">
                </td>
                <td style="width:50%">
                    <div class="invoice-title">INVOICE</div>
                </td>
            </tr>
        </table>

        <div class="issuer-name">Luis Carneiro</div>

        <div class="clearfix" style="margin-bottom:20px;">
            <div class="col-left">
                <div class="invoice-to-label">Invoice to:</div>
                <div class="invoice-to-name">{{ $invoice->customer_name }}</div>
            </div>
            <div class="col-right">
                <table class="meta-table" style="width:auto; margin-left:auto;">
                    <tr>
                        <td class="label">Invoice#</td>
                        <td class="font-weight-bold">{{ $invoice->number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td class="font-weight-bold">{{ $invoice->issue_date->format('Y-m-d') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:40px">SL.</th>
                    <th>Item Description</th>
                    <th style="width:110px">Price</th>
                    <th style="width:60px">Qty.</th>
                    <th style="width:110px">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $index => $line)
                    <tr>
                        <td>{{ $index + 1 }}</td>
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

        <div class="clearfix">
            <div class="col-left">
                <div class="payment-info-title">Payment Info:</div>
                <table class="meta-table">
                    <tr>
                        <td class="label">Method:</td>
                        <td>{{ $invoice->payment_method ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-right">
                <table class="totals-table">
                    <tr>
                        <td class="label">Sub Total:</td>
                        <td class="text-right">{{ $money($invoice->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="text-right">{{ $money($invoice->tax) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total:</td>
                        <td class="text-right">{{ $money($invoice->total) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="terms-section">
            <div class="terms-title">Terms &amp; Conditions</div>
            <div class="terms-text">{{ $invoice->terms_and_conditions ?: 'Once payment has been made, the customer is entitled to up to 2 inspections' }}</div>
        </div>

        <div class="signature-section clearfix">
            <div class="signature-box">
                <img src="{{ public_path('img/firma-cropped.png') }}" alt="Firma" style="max-width:80%;max-height:35px;">
                <div class="signature-line">Luis Carneiro</div>
            </div>
        </div>

    </div>
</body>
</html>
