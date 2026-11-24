<?php

namespace App\Core\Tenancy\Resolvers;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\Contracts\TenantResolverInterface;

/**
 * Orquesta múltiples estrategias de resolución de tenant (Strategy Pattern).
 * Prueba los resolvers registrados en orden hasta que uno devuelva una
 * empresa. Nuevas estrategias (subdominio, dominio propio, header, API key)
 * se añaden con `register()` sin tocar el código que ya consume el manager.
 */
class TenantResolverManager
{
    /** @var array<int, TenantResolverInterface> */
    private array $resolvers = [];

    public function register(TenantResolverInterface $resolver): static
    {
        $this->resolvers[$resolver->key()] = $resolver;

        return $this;
    }

    public function resolve(): ?Company
    {
        foreach ($this->resolvers as $resolver) {
            $company = $resolver->resolve();

            if ($company !== null) {
                return $company;
            }
        }

        return null;
    }

    /**
     * @return array<int, TenantResolverInterface>
     */
    public function resolvers(): array
    {
        return $this->resolvers;
    }
}
