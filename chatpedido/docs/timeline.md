# Activity Timeline

**No reemplaza a Laravel Auditing.** Son dos registros distintos, con propósitos distintos:

| | Laravel Auditing | Timeline |
|---|---|---|
| Qué registra | Cambios crudos de columnas (`old_values`/`new_values`) en modelos `Auditable` | Acciones funcionales, legibles para un humano |
| Ejemplo | `User#12: email changed from "a@b.com" to "c@d.com"` | "Luis creó un cliente" |
| Dónde vive | tabla `audits` (paquete `owen-it/laravel-auditing`) | tabla `timeline_entries` (`App\Core\Timeline`) |
| Panel | Resource **Auditoría** | Resource **Timeline** |

Usa Auditing para trazabilidad técnica/forense de datos; usa Timeline para contar la historia del negocio de cara al usuario.

## Uso

```php
use App\Core\Timeline\TimelineService;

app(TimelineService::class)->log(
    action: 'customer.created',
    description: "Luis creó un cliente",
    subject: $customer,          // opcional: modelo relacionado (relación polimórfica)
    properties: ['source' => 'api'], // opcional: metadatos adicionales
);
```

Helper equivalente:

```php
timeline('customer.created', 'Luis creó un cliente', $customer);
timeline(); // devuelve el TimelineService para encadenar
```

`company_id` y `user_id` se rellenan automáticamente desde `tenant()` — no hace falta pasarlos.

## Integración en tus propios módulos

El patrón recomendado es un `Observer` del modelo que llama a `timeline()->log()` en `created`/`updated`/`deleted` con una descripción legible (ver `App\Core\Companies\Observers\CompanyObserver` como referencia):

```php
class CustomerObserver
{
    public function created(Customer $customer): void
    {
        timeline('customer.created', "Se creó el cliente \"{$customer->name}\"", $customer);
    }
}
```

## Panel Filament

`/admin/timeline-entries` — solo lectura, ordenado por fecha descendente, filtrado automáticamente por la empresa activa (`TimelineEntry` extiende `TenantModel`).
