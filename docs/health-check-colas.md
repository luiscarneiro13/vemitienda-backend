# Health check de la cola de trabajos

## Endpoint

```
GET /api/v3/health/queue
```

No requiere autenticación.

## Qué comprueba

El sistema usa `QUEUE_CONNECTION=database` y depende del servicio `queue-worker`
(`php artisan queue:work`) para procesar jobs (emails, descargas de video, generación
de contenido con IA, etc.). Si ese proceso se cae, los jobs se quedan encolados sin avisar.

Este endpoint no se limita a mirar si hay filas en la tabla `jobs` (una cola vacía puede
significar tanto "todo al día" como "el worker está caído"). En su lugar, despacha un job
real de prueba (`App\Jobs\QueueHealthCheckJob`) con un token único y espera hasta 5
segundos a que el worker lo procese y marque un valor en cache. Así se confirma que el
worker está efectivamente consumiendo la cola, no solo que el proceso existe.

Archivos relevantes:
- `vemitienda/app/Http/Controllers/API/V3/HealthController.php`
- `vemitienda/app/Jobs/QueueHealthCheckJob.php`
- `vemitienda/routes/api.php`

## Respuestas

**Cola corriendo (200):**

```json
{
  "status": 200,
  "message": "La cola está corriendo.",
  "success": true,
  "data": {
    "running": true,
    "seconds_elapsed": 0.4
  }
}
```

**Cola caída / no procesa jobs (503):**

```json
{
  "status": 503,
  "message": "La cola no está procesando jobs.",
  "success": false,
  "error": {
    "running": false,
    "timeout_seconds": 5,
    "pending_jobs": 12,
    "failed_jobs": 0
  }
}
```

## Uso

Pensado para monitoreo externo (uptime checks, alertas) o para verificar manualmente
tras un despliegue:

```bash
curl https://tu-dominio/api/v3/health/queue
```

Un `503` sostenido indica que el contenedor `queue-worker` no está corriendo o quedó
colgado; revisar con `docker compose ps` y `docker compose logs queue-worker`.
