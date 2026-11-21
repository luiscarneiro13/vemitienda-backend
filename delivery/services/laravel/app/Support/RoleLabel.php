<?php

namespace App\Support;

/**
 * Traduce el slug técnico de un rol de Spatie (usado en hasRole(), en
 * seeders y en el código de autorización) a un label legible en español
 * para la interfaz. No modifica el dato real almacenado.
 */
class RoleLabel
{
    private const LABELS = [
        'super_admin' => 'Super Administrador',
        'admin' => 'Administrador',
        'manager' => 'Gerente',
        'store_owner' => 'Dueño de tienda',
        'store_admin' => 'Administrador de tienda',
        'user' => 'Usuario',
    ];

    public static function for(string $slug): string
    {
        return self::LABELS[$slug] ?? $slug;
    }
}
