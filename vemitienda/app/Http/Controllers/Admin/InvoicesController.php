<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoicesController extends Controller
{
    /**
     * Mockup: lista facturas leyendo el CSV de ejemplo en database/mock/activity-history.csv.
     * Cuando exista el modelo real de facturación, esto debe reemplazarse por una consulta al repositorio.
     */
    public function index()
    {
        $filtrar = request()->get('query');
        $paymentMethod = request()->get('payment_method');

        $rows = $this->readCsv(database_path('mock/activity-history.csv'));

        $paymentMethods = $rows->pluck('Payment Method')->filter()->unique()->sort()->values();

        if ($filtrar) {
            $rows = $rows->filter(function ($row) use ($filtrar) {
                $filtrar = mb_strtolower($filtrar);
                return str_contains(mb_strtolower($row['Transaction ID'] ?? ''), $filtrar)
                    || str_contains(mb_strtolower($row['Peer'] ?? ''), $filtrar)
                    || str_contains(mb_strtolower($row['Status'] ?? ''), $filtrar)
                    || str_contains(mb_strtolower($row['Type'] ?? ''), $filtrar);
            })->values();
        }

        if ($paymentMethod) {
            $rows = $rows->filter(function ($row) use ($paymentMethod) {
                return ($row['Payment Method'] ?? '') === $paymentMethod;
            })->values();
        }

        $perPage = 15;
        $page = (int) request()->get('page', 1);

        $paginator = new LengthAwarePaginator(
            $rows->slice(($page - 1) * $perPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $data['infoData'] = $paginator;
        $data['paymentMethods'] = $paymentMethods;

        return view('Admin.Invoices.index', ['data' => $data]);
    }

    public function show(string $transaction)
    {
        $rows = $this->readCsv(database_path('mock/activity-history.csv'));

        $item = $rows->first(fn ($row) => ($row['Transaction ID'] ?? '') === $transaction);

        abort_if(!$item, 404);

        $data['item'] = $item;

        return view('Admin.Invoices.show', ['data' => $data]);
    }

    private function readCsv(string $path)
    {
        $rows = collect();

        if (!file_exists($path)) {
            return $rows;
        }

        $handle = fopen($path, 'r');

        // El CSV trae BOM UTF-8, que rompe el parseo de comillas del primer header si no se descarta antes.
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
