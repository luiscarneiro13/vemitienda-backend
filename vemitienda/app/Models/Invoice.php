<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    /**
     * Métodos de pago reales encontrados en database/mock/activity-history.csv.
     * No hay tabla/catálogo en base de datos para esto a propósito.
     */
    const PAYMENT_METHODS = [
        'Wally',
        'Binance Pay (USDT)',
        'Binance Pay (USDC)',
        'Binance Pay (BTC)',
        'Binance Pay (BUSD)',
        'Payoneer USD',
        'Google Pay USD',
        'Venezuela Banco De Venezuela',
        'Venezuela Banco Occidental De Descuento',
        'Venezuela Banco Nacional De Credito',
        'Venezuela Banco Del Tesoro',
        'Venezuela Bancamiga',
        'Venezuela Banco Mercantil',
        'Venezuela Banesco',
        'Venezuela Banco Del Sur',
        'Venezuela BBVA Banco Provincial',
    ];

    protected $fillable = [
        'number',
        'issue_date',
        'customer_name',
        'customer_email',
        'payment_method',
        'items',
        'subtotal',
        'tax',
        'total',
        'terms_and_conditions',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'issue_date' => 'date',
    ];

    /**
     * Genera un número de factura estilo "C4AEDU44DANE842B" (16 caracteres
     * alfanuméricos en mayúsculas), garantizando que no exista ya en la tabla.
     */
    public static function generateNumber(): string
    {
        do {
            $number = strtoupper(Str::random(16));
        } while (self::where('number', $number)->exists());

        return $number;
    }
}
