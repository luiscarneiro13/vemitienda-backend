# Contribuir

## Entorno de desarrollo

Todo corre en Docker. Consulta [docs/installation.md](docs/installation.md) para levantar el entorno.

```bash
docker compose up -d
docker compose exec delivery-app php artisan migrate --seed
```

## Estilo de código

- PSR-12, aplicado con [Laravel Pint](https://laravel.com/docs/pint):
  ```bash
  docker compose exec delivery-app ./vendor/bin/pint
  ```
- Sin lógica de negocio en Controllers ni en Filament Resources — va en `app/Services`.
- Validación en Form Requests; autorización en Policies o en `$user->can('recurso.accion')`.
- Commits en inglés, formato [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `chore:`, `docs:`, `refactor:`, `test:`).

## Tests

```bash
docker compose exec delivery-app php artisan test
```

## Añadir un módulo nuevo

Sigue el patrón descrito en [docs/architecture.md](docs/architecture.md#capas): Model → Migration → Service → Policy → Filament Resource, terminando con `php artisan shield:generate --all --panel=admin` para que el nuevo Resource tenga sus permisos.

## Pull requests

1. Rama descriptiva a partir de `main` (`feat/nombre-corto`, `fix/nombre-corto`).
2. Verifica que `pint` y `php artisan test` pasen antes de abrir el PR.
3. Describe el cambio y su motivación en el PR, no solo el "qué".
