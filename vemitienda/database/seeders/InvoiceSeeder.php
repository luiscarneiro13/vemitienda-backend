<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Carga facturas de ejemplo a partir de database/mock/activity-history.csv.
     * La primera fila del CSV (la más reciente) queda como el último registro
     * insertado en la tabla, ya que se recorre el CSV en orden inverso.
     *
     * @return void
     */
    public function run()
    {
        if (Invoice::count() > 0) {
            return;
        }

        $rows = $this->readCsv(database_path('mock/activity-history.csv'));

        foreach ($rows->reverse()->values() as $row) {
            $peer = trim($row['Peer Full Name'] ?? '') ?: trim($row['Peer'] ?? '');
            $customerName = ($peer !== '' && $peer !== '-') ? $peer : 'N/A';
            $customerEmail = filter_var($peer, FILTER_VALIDATE_EMAIL) ? $peer : null;

            $paymentMethod = trim($row['Payment Method'] ?? '');
            $paymentMethod = in_array($paymentMethod, Invoice::PAYMENT_METHODS, true) ? $paymentMethod : null;

            $amount = (float) ($row['Funds To Send Amount'] ?? 0);

            $items = [[
                'description' => 'Web Design',
                'price' => $amount,
                'quantity' => 1,
                'total' => $amount,
            ]];

            $status = match ($row['Status'] ?? null) {
                'Completada' => 'paid',
                'Cancelada' => 'cancelled',
                default => 'draft',
            };

            $createdAt = Carbon::parse($row['Created At (UTC)'] ?? now());
            $updatedAt = Carbon::parse($row['Updated At (UTC)'] ?? $createdAt);

            $invoice = new Invoice([
                'number' => Invoice::generateNumber(),
                'issue_date' => $createdAt->toDateString(),
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'payment_method' => $paymentMethod,
                'items' => $items,
                'subtotal' => $amount,
                'tax' => 0,
                'total' => $amount,
                'terms_and_conditions' => null,
                'status' => $status,
            ]);

            $invoice->timestamps = false;
            $invoice->created_at = $createdAt;
            $invoice->updated_at = $updatedAt;
            $invoice->save();
        }
    }

    private function readCsv(string $path)
    {
        $rows = collect();

        if (!file_exists($path)) {
            return $rows;
        }

        $handle = fopen($path, 'r');

        if (fread($handle, 3) !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $rows->push(array_combine($headers, $row));
        }

        fclose($handle);

        return $rows;
    }
}
