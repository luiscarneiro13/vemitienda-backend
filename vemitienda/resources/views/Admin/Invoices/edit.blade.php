@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-file-invoice-dollar"></i> Editar Factura</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('facturas.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>

    <form action="{{ route('facturas.update', $invoice->number) }}" method="POST" id="invoice-edit-form">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Número de factura</label>
                        <input type="text" class="form-control" value="{{ $invoice->number }}" readonly disabled>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Método de pago</label>
                        <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                            <option value="">Seleccione un método</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method }}" {{ $invoice->payment_method === $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <x-text name="customer_name" columns="6" label="Nombre del cliente" required="true"
                    placeholder="Ingrese el nombre del cliente..." value="{{ $invoice->customer_name }}" />

                <x-text name="customer_email" columns="6" type="email" label="Correo electrónico"
                    placeholder="Ingrese el correo del cliente..." value="{{ $invoice->customer_email }}" />
            </div>

            <div class="row">
                <x-text name="issue_date" columns="6" type="date" label="Fecha de emisión" required="true"
                    value="{{ $invoice->issue_date->format('Y-m-d') }}" />
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="control-label mb-0">Ítems <span class="text-danger">*</span></label>
                        <button type="button" class="btn btn-outline-dark btn-xs" id="add-item-btn">
                            <i class="fa fa-plus"></i> Agregar ítem
                        </button>
                    </div>

                    <table class="table table-sm table-bordered" id="items-table" style="font-size:12px">
                        <thead class="thead-light">
                            <tr>
                                <th width="40">SL.</th>
                                <th>Item Description</th>
                                <th width="140">Price</th>
                                <th width="90">Qty.</th>
                                <th width="140">Total</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            @foreach ($invoice->items as $line)
                                <tr class="item-row">
                                    <td class="text-center align-middle sl-cell">{{ $loop->iteration }}</td>
                                    <td>
                                        <input type="text" name="items[{{ $loop->index }}][description]"
                                            class="form-control form-control-sm item-description"
                                            value="{{ $line['description'] ?? '' }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0"
                                            name="items[{{ $loop->index }}][price]"
                                            class="form-control form-control-sm item-price"
                                            value="{{ $line['price'] ?? 0 }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="1" min="1"
                                            name="items[{{ $loop->index }}][quantity]"
                                            class="form-control form-control-sm item-quantity"
                                            value="{{ $line['quantity'] ?? 1 }}" required>
                                    </td>
                                    <td class="text-right align-middle item-total-cell">
                                        {{ number_format(($line['price'] ?? 0) * ($line['quantity'] ?? 1), 2) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-outline-danger btn-xs remove-item-btn">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @error('items')
                        <span class="text-danger"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <table class="w-100" style="font-size:13px">
                        <tr>
                            <td class="text-muted">Sub Total:</td>
                            <td class="text-right" id="summary-subtotal">$0.00</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tax:</td>
                            <td class="text-right" id="summary-tax">$0.00</td>
                        </tr>
                        <tr style="border-top:2px solid #333">
                            <td class="font-weight-bold pt-2">Total:</td>
                            <td class="text-right font-weight-bold pt-2" id="summary-total">$0.00</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <x-textarea name="terms_and_conditions" columns="12" label="Términos y condiciones"
                    placeholder="Ingrese los términos y condiciones..." value="{{ $invoice->terms_and_conditions }}" />
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-dark float-right"><i class="fa fa-save"></i> Actualizar</button>
        </div>
    </form>
</div>

@endsection

@section('js')
<script>
    (function () {
        const itemsBody = document.getElementById('items-body');
        const addItemBtn = document.getElementById('add-item-btn');
        let rowIndex = {{ max(count($invoice->items), 1) }};

        function money(value) {
            return '$' + (isNaN(value) ? 0 : value).toFixed(2);
        }

        function recalculateRow(row) {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const total = Math.round(price * quantity * 100) / 100;
            row.querySelector('.item-total-cell').textContent = total.toFixed(2);
        }

        function recalculateSummary() {
            let subtotal = 0;
            itemsBody.querySelectorAll('.item-row').forEach(function (row) {
                recalculateRow(row);
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                subtotal += Math.round(price * quantity * 100) / 100;
            });
            document.getElementById('summary-subtotal').textContent = money(subtotal);
            document.getElementById('summary-tax').textContent = money(0);
            document.getElementById('summary-total').textContent = money(subtotal);
        }

        function renumberRows() {
            itemsBody.querySelectorAll('.item-row').forEach(function (row, idx) {
                row.querySelector('.sl-cell').textContent = idx + 1;
            });
        }

        function addRow() {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td class="text-center align-middle sl-cell"></td>
                <td>
                    <input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm item-description" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${rowIndex}][price]" class="form-control form-control-sm item-price" value="0" required>
                </td>
                <td>
                    <input type="number" step="1" min="1" name="items[${rowIndex}][quantity]" class="form-control form-control-sm item-quantity" value="1" required>
                </td>
                <td class="text-right align-middle item-total-cell">0.00</td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger btn-xs remove-item-btn"><i class="fa fa-trash"></i></button>
                </td>
            `;
            itemsBody.appendChild(tr);
            rowIndex++;
            renumberRows();
            recalculateSummary();
        }

        addItemBtn.addEventListener('click', addRow);

        itemsBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-item-btn');
            if (!btn) return;
            if (itemsBody.querySelectorAll('.item-row').length <= 1) return;
            btn.closest('.item-row').remove();
            renumberRows();
            recalculateSummary();
        });

        itemsBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('item-price') || e.target.classList.contains('item-quantity')) {
                recalculateSummary();
            }
        });

        recalculateSummary();
    })();
</script>
@endsection
