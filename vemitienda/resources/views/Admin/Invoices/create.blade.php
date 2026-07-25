@extends('layouts.adminlte.index')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-file-invoice"></i> Crear Factura</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('facturas.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('facturas.store') }}" method="POST" id="form-factura">
            @csrf()
            <div class="card-body">
                <div class="row">
                    <x-text columns="6" label="Nombre del Cliente" required="true" name="customer_name"
                        placeholder="Ingrese el nombre del cliente..." />
                    <x-text columns="6" type="email" label="Correo del Cliente" name="customer_email"
                        placeholder="Ingrese el correo del cliente..." />
                    <x-select columns="6" label="Método de Pago" name="payment_method"
                        :datos="collect(\App\Models\Invoice::PAYMENT_METHODS)->map(fn($m) => (object)['id' => $m, 'label' => $m])" />
                    <x-text columns="6" type="date" label="Fecha de Emisión" required="true" name="issue_date"
                        value="{{ old('issue_date', date('Y-m-d')) }}" />
                    <x-textarea columns="12" label="Términos y Condiciones" name="terms_and_conditions"
                        placeholder="Ingrese los términos y condiciones..." />
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6 class="text-default">Ítems de la Factura</h6>
                        <table class="table table-bordered table-sm" id="tabla-items">
                            <thead>
                                <tr>
                                    <th style="width: 40%">Descripción</th>
                                    <th style="width: 15%">Precio</th>
                                    <th style="width: 15%">Cantidad</th>
                                    <th style="width: 20%">Total</th>
                                    <th style="width: 10%"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                {{-- Filas de ítems generadas por JS --}}
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-sm btn-dark" id="btn-add-item">
                            <i class="fa fa-plus"></i> Agregar ítem
                        </button>

                        @error('items')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td class="text-right" id="subtotal-display">$0.00</td>
                                </tr>
                                <tr>
                                    <th>Tax</th>
                                    <td class="text-right">
                                        <input type="text" class="form-control form-control-sm text-right"
                                            value="$0.00" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-right"><strong id="total-display">$0.00</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn-sm btn btn-dark float-right"><i class="fa fa-save"></i>
                    Guardar</button>
            </div>
        </form>
    </div>

</div>
@endsection
@section('js')
<script>
    (function () {
        var itemIndex = 0;

        function filaItem() {
            var idx = itemIndex++;
            return '' +
                '<tr data-row="' + idx + '">' +
                '<td><input type="text" name="items[' + idx + '][description]" class="form-control form-control-sm item-description" required></td>' +
                '<td><input type="number" step="0.01" min="0" name="items[' + idx + '][price]" class="form-control form-control-sm item-price" value="0" required></td>' +
                '<td><input type="number" step="1" min="1" name="items[' + idx + '][quantity]" class="form-control form-control-sm item-quantity" value="1" required></td>' +
                '<td class="text-right align-middle item-total">$0.00</td>' +
                '<td class="text-center align-middle"><button type="button" class="btn btn-xs btn-danger btn-remove-item"><i class="fa fa-trash"></i></button></td>' +
                '</tr>';
        }

        function formatMoney(value) {
            return '$' + (isNaN(value) ? 0 : value).toFixed(2);
        }

        function recalcular() {
            var subtotal = 0;

            $('#items-body tr').each(function () {
                var price = parseFloat($(this).find('.item-price').val()) || 0;
                var quantity = parseInt($(this).find('.item-quantity').val()) || 0;
                var total = price * quantity;

                $(this).find('.item-total').text(formatMoney(total));
                subtotal += total;
            });

            $('#subtotal-display').text(formatMoney(subtotal));
            $('#total-display').text(formatMoney(subtotal));
        }

        function agregarFila() {
            $('#items-body').append(filaItem());
        }

        $('#btn-add-item').on('click', function () {
            agregarFila();
        });

        $(document).on('click', '.btn-remove-item', function () {
            $(this).closest('tr').remove();
            recalcular();
        });

        $(document).on('input', '.item-price, .item-quantity', function () {
            recalcular();
        });

        // Al menos una fila inicial
        agregarFila();
        recalcular();
    })();
</script>
@endsection
