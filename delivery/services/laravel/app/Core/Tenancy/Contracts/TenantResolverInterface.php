<?php

namespace App\Core\Tenancy\Contracts;

use App\Core\Companies\Models\Company;

/**
 * Estrategia de resolución de la empresa activa para la petición actual.
 * Cada implementación resuelve el tenant por un mecanismo distinto
 * (usuario autenticado, subdominio, dominio propio, header, API key...).
 */
interface TenantResolverInterface
{
    /**
     * Nombre corto de la estrategia (p. ej. "user", "subdomain", "header").
     */
    public function key(): string;

    /**
     * Resuelve la empresa activa, o null si esta estrategia no aplica
     * al contexto actual (p. ej. no hay usuario autenticado).
     */
    public function resolve(): ?Company;
}
