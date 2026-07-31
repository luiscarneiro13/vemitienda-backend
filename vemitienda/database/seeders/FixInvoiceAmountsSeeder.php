<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixInvoiceAmountsSeeder extends Seeder
{
    /**
     * Repara el bug de InvoiceSeeder: ese seeder guardó "Funds To Send Amount"
     * (lo que el peer envió) como monto de la factura, cuando debía usarse
     * "Funds To Receive Amount" (lo que realmente se recibió).
     *
     * Relee database/mock/activity-history.csv y, para cada fila, ubica la
     * factura original por (customer_name + created_at exactos: únicos por
     * fila, ya que InvoiceSeeder los copió tal cual del CSV) y corrige
     * items/subtotal/total. No toca facturas editadas a mano ni facturas ya
     * corregidas: es seguro de correr más de una vez.
     *
     * 5 filas del CSV (tipo "Pago"/"Fondos enviados") no tienen
     * "Funds To Receive Amount" porque son dinero saliente sin contraparte:
     * para esas se deja el monto tal cual estaba (fallback a Send Amount) y
     * se listan aparte en el resumen para que se revisen a mano si hace falta.
     *
     * Uso en producción:
     *   php artisan db:seed --class=FixInvoiceAmountsSeeder --force
     */
    public function run()
    {
        $rows = $this->readCsv(database_path('mock/activity-history.csv'));

        if ($rows->isEmpty()) {
            $this->output('No se encontró o está vacío: database/mock/activity-history.csv');
            return;
        }

        $toFix = [];
        $alreadyFixed = [];
        $notFound = [];
        $shapeMismatch = [];
        $amountMismatch = [];
        $fallbackRows = [];

        foreach ($rows as $row) {
            $peer = trim($row['Peer Full Name'] ?? '') ?: trim($row['Peer'] ?? '');
            $customerName = ($peer !== '' && $peer !== '-') ? $peer : 'N/A';
            $createdAt = Carbon::parse($row['Created At (UTC)'] ?? now());
            $transactionId = $row['Transaction ID'] ?? '-';

            $oldAmount = round((float) ($row['Funds To Send Amount'] ?? 0), 2);
            $receiveRaw = trim($row['Funds To Receive Amount'] ?? '');
            $newAmount = $receiveRaw !== '' ? round((float) $receiveRaw, 2) : $oldAmount;

            if ($receiveRaw === '') {
                $fallbackRows[] = [$transactionId, $customerName, $row['Type'] ?? '-', $oldAmount];
            }

            $invoice = Invoice::where('customer_name', $customerName)
                ->where('created_at', $createdAt)
                ->first();

            if (!$invoice) {
                $notFound[] = [$transactionId, $customerName, $createdAt->toDateTimeString()];
                continue;
            }

            $items = $invoice->items;

            // Solo tocamos facturas con la forma exacta que crea InvoiceSeeder (1 ítem
            // "Web Design"). Si alguien la editó a mano después, no se auto-corrige.
            if (!is_array($items) || count($items) !== 1 || ($items[0]['description'] ?? null) !== 'Web Design') {
                $shapeMismatch[] = [$invoice->number, $customerName, 'items con forma distinta a la esperada'];
                continue;
            }

            $currentAmount = round((float) $invoice->subtotal, 2);

            if ($this->sameAmount($currentAmount, $newAmount)) {
                $alreadyFixed[] = $invoice->number;
                continue;
            }

            if (!$this->sameAmount($currentAmount, $oldAmount)) {
                // El monto actual no coincide ni con el bug ni con el valor esperado:
                // no se toca, se reporta para revisión manual.
                $amountMismatch[] = [$invoice->number, $customerName, $currentAmount, $oldAmount, $newAmount];
                continue;
            }

            $toFix[] = ['invoice' => $invoice, 'newAmount' => $newAmount];
        }

        $this->printSummary($rows->count(), $toFix, $alreadyFixed, $notFound, $shapeMismatch, $amountMismatch, $fallbackRows);

        if (empty($toFix)) {
            $this->output('Nada para corregir.');
            return;
        }

        if (isset($this->command) && !$this->command->confirm(count($toFix) . ' factura(s) serán modificadas. ¿Continuar?')) {
            $this->output('Cancelado: no se modificó nada.');
            return;
        }

        DB::transaction(function () use ($toFix) {
            foreach ($toFix as $fix) {
                /** @var Invoice $invoice */
                $invoice = $fix['invoice'];
                $newAmount = $fix['newAmount'];

                $items = $invoice->items;
                $items[0]['price'] = $newAmount;
                $items[0]['total'] = $newAmount;

                $invoice->items = $items;
                $invoice->subtotal = $newAmount;
                $invoice->total = $newAmount; // tax siempre es 0 en este módulo
                $invoice->save();
            }
        });

        $this->output(count($toFix) . ' factura(s) corregidas correctamente.');
    }

    /**
     * Compara montos con tolerancia, evitando falsos negativos por precisión flotante.
     */
    private function sameAmount(float $a, float $b): bool
    {
        return abs($a - $b) < 0.005;
    }

    private function printSummary(
        int $totalCsvRows,
        array $toFix,
        array $alreadyFixed,
        array $notFound,
        array $shapeMismatch,
        array $amountMismatch,
        array $fallbackRows
    ) {
        $this->output("Filas en el CSV: {$totalCsvRows}");
        $this->output('A corregir: ' . count($toFix));
        $this->output('Ya corregidas (se saltan): ' . count($alreadyFixed));
        $this->output('No encontradas en BD: ' . count($notFound));
        $this->output('Con forma de items inesperada (no se tocan): ' . count($shapeMismatch));
        $this->output('Con monto actual inesperado (no se tocan): ' . count($amountMismatch));
        $this->output('Sin "Funds To Receive Amount" en el CSV (se usó el monto original): ' . count($fallbackRows));

        if (isset($this->command)) {
            if (!empty($toFix)) {
                $this->command->table(
                    ['Número', 'Cliente', 'Monto actual (bug)', 'Monto nuevo'],
                    collect($toFix)->map(fn ($fix) => [
                        $fix['invoice']->number,
                        $fix['invoice']->customer_name,
                        $fix['invoice']->subtotal,
                        $fix['newAmount'],
                    ])
                );
            }

            if (!empty($notFound)) {
                $this->command->warn('No encontradas en BD (revisar a mano):');
                $this->command->table(['Transaction ID', 'Cliente', 'Created At'], $notFound);
            }

            if (!empty($shapeMismatch)) {
                $this->command->warn('Forma de items inesperada (revisar a mano):');
                $this->command->table(['Número', 'Cliente', 'Motivo'], $shapeMismatch);
            }

            if (!empty($amountMismatch)) {
                $this->command->warn('Monto actual no coincide ni con el bug ni con el esperado (revisar a mano):');
                $this->command->table(['Número', 'Cliente', 'Monto actual', 'Monto bug esperado', 'Monto nuevo esperado'], $amountMismatch);
            }

            if (!empty($fallbackRows)) {
                $this->command->warn('Filas sin "Funds To Receive Amount" en el CSV (quedaron con el monto original, revisar si corresponde):');
                $this->command->table(['Transaction ID', 'Cliente', 'Type', 'Monto usado'], $fallbackRows);
            }
        }
    }

    private function output(string $message)
    {
        if (isset($this->command)) {
            $this->command->info($message);
        } else {
            echo $message . PHP_EOL;
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
