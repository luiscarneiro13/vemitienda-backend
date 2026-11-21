# Media Engine

Gestiona logos, documentos, imágenes, videos y adjuntos de cualquier modelo (relación polimórfica), con el almacenamiento físico desacoplado mediante Strategy Pattern.

## Uso

```php
use App\Core\Media\MediaManager;

$media = app(MediaManager::class);

$company->logo = $media->attach($company, $request->file('logo'), 'logo');

$media->url($company->logo);              // URL pública del archivo
$media->for($company, 'logo');             // Collection<Media> de esa colección
$media->for($company);                     // todos los archivos del modelo, cualquier colección
$media->delete($mediaRecord);              // borra el archivo físico + el registro
```

Helper equivalente: `media()` devuelve el `MediaManager`.

## Driver activo (Strategy Pattern)

`App\Core\Media\Contracts\MediaDriverInterface` define el contrato (`store`, `delete`, `url`). Hoy solo existe `LocalMediaDriver` (usa el disco de Laravel configurado en `MEDIA_DISK`, por defecto `public`).

```php
// config/core.php
'media' => [
    'driver' => env('MEDIA_DRIVER', 'local'),
    'disk' => env('MEDIA_DISK', 'public'),
],
```

### Añadir S3 / Minio / Azure

1. Implementa `MediaDriverInterface` (p. ej. `S3MediaDriver`), delegando en el disco de Laravel correspondiente (`Storage::disk('s3')`) — Laravel ya abstrae el proveedor real vía `config/filesystems.php`, así que normalmente ni siquiera necesitas un driver de Media distinto por proveedor: basta con apuntar `MEDIA_DISK` a un disco S3/Minio/Azure ya configurado en `filesystems.php` y seguir usando `LocalMediaDriver` (el nombre es histórico, la clase solo delega en `Storage::disk($disk)`, sea cual sea ese disco).
2. Si necesitas lógica específica del proveedor (URLs firmadas con expiración, metadatos extra), crea el driver dedicado y regístralo en `CoreServiceProvider`:

```php
$this->app->bind(MediaDriverInterface::class, fn () => match (config('core.media.driver')) {
    's3' => new S3MediaDriver(config('core.media.disk')),
    default => new LocalMediaDriver(config('core.media.disk')),
});
```

Ningún otro punto del Core cambia — `MediaManager` solo depende del contrato.

## Modelo `Media`

Tabla `media`: relación polimórfica (`model_type`/`model_id`), `collection` (para agrupar adjuntos de un mismo modelo — p. ej. "logo" vs "documents"), `disk`, `path`, metadatos del archivo original, y quién lo subió (`created_by`).
