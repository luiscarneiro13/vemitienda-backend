<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicesController extends Controller
{
    public function index()
    {
        $filtrar = request()->get('query');
        $paymentMethod = request()->get('payment_method');

        $invoices = Invoice::query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where(function ($q) use ($filtrar) {
                    $q->where('number', 'like', "%{$filtrar}%")
                        ->orWhere('customer_name', 'like', "%{$filtrar}%")
                        ->orWhere('status', 'like', "%{$filtrar}%");
                });
            })
            ->when($paymentMethod, fn ($q) => $q->where('payment_method', $paymentMethod))
            ->orderBy('issue_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $data['infoData'] = $invoices;
        $data['paymentMethods'] = Invoice::PAYMENT_METHODS;

        return view('Admin.Invoices.index', ['data' => $data]);
    }

    public function show(string $number)
    {
        $invoice = Invoice::where('number', $number)->firstOrFail();

        return view('Admin.Invoices.show', ['data' => ['item' => $invoice]]);
    }

    public function create()
    {
        $data['paymentMethods'] = Invoice::PAYMENT_METHODS;

        return view('Admin.Invoices.create', $data);
    }

    public function store()
    {
        $validated = request()->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'nullable|email',
            'payment_method' => 'nullable|in:' . implode(',', Invoice::PAYMENT_METHODS),
            'issue_date' => 'required|date',
            'terms_and_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $total = round($item['price'] * $item['quantity'], 2);
            $subtotal += $total;

            $items[] = [
                'description' => $item['description'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $total,
            ];
        }

        $subtotal = round($subtotal, 2);
        $tax = 0;
        $total = $subtotal + $tax;

        Invoice::create([
            'number' => Invoice::generateNumber(),
            'issue_date' => $validated['issue_date'],
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
        ]);

        return redirect()->route('facturas.index');
    }

    public function edit(string $number)
    {
        $invoice = Invoice::where('number', $number)->firstOrFail();

        $data['invoice'] = $invoice;
        $data['paymentMethods'] = Invoice::PAYMENT_METHODS;

        return view('Admin.Invoices.edit', $data);
    }

    public function update(string $number)
    {
        $invoice = Invoice::where('number', $number)->firstOrFail();

        $validated = request()->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'payment_method' => 'nullable|in:' . implode(',', Invoice::PAYMENT_METHODS),
            'issue_date' => 'required|date',
            'terms_and_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $items = array_map(function ($line) {
            $price = (float) $line['price'];
            $quantity = (int) $line['quantity'];

            return [
                'description' => $line['description'],
                'price' => $price,
                'quantity' => $quantity,
                'total' => round($price * $quantity, 2),
            ];
        }, $validated['items']);

        $subtotal = round(array_sum(array_column($items, 'total')), 2);

        $invoice->update([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'issue_date' => $validated['issue_date'],
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ]);

        return redirect()->route('facturas.show', $invoice->number);
    }

    public function pdf(string $number)
    {
        $invoice = Invoice::where('number', $number)->firstOrFail();

        $pdf = Pdf::loadView('Admin.Invoices.pdf', ['invoice' => $invoice]);
        $pdf->getDomPDF()->add_info('Author', 'Luis Carneiro');
        $pdf->getDomPDF()->add_info('Creator', 'Luis Carneiro - Web Design');
        $pdf->getDomPDF()->add_info('Subject', "Factura {$invoice->number}");

        return $pdf->stream("factura-{$invoice->number}.pdf");
    }
}
