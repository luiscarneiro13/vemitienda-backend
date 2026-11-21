# Settings Engine

No es una simple tabla clave/valor: `App\Core\Settings\SettingsManager` soporta **grupos**, **tipos** (con casting automático), **validación** opcional al escribir, y cachea el conjunto completo en memoria.

## Piezas

- `SettingsRepository` — acceso a la tabla `settings` (`key`, `value`, `type`, `group`).
- `SettingsCache` — envoltorio sobre `Cache::rememberForever()`, invalidado en cada escritura.
- `SettingsManager` — orquesta ambos y expone la API pública.

## Uso

```php
use App\Core\Settings\SettingsManager;

$settings = app(SettingsManager::class);

$settings->set('app_name', 'Acme Corp', 'string', 'general');
$settings->set('max_users', 25, 'integer', 'limits');
$settings->set('maintenance_mode', true, 'boolean');
$settings->set('allowed_ips', ['127.0.0.1'], 'array', 'security');

$settings->get('app_name');                 // 'Acme Corp' (string)
$settings->get('max_users');                // 25 (int)
$settings->get('missing_key', 'fallback');   // 'fallback'
$settings->group('general');                 // ['app_name' => 'Acme Corp', ...]
```

### Tipos soportados

`string` (por defecto), `integer`, `float`, `boolean`, `array` (serializado como JSON en la columna `value`).

### Validación al escribir

```php
$settings->set('support_email', $value, 'string', 'general', ['required', 'email']);
```

Lanza `Illuminate\Validation\ValidationException` si no cumple las reglas — igual que cualquier validación estándar de Laravel.

## Helper `setting()`

```php
setting('app_name');                  // atajo a SettingsManager::get()
setting('app_name', 'Mi App');         // con valor por defecto
setting();                             // devuelve el SettingsManager para encadenar
```

## Panel Filament

`App\Filament\Pages\Settings` (`/admin/settings`) es la página de configuración general del sistema — persiste sus campos vía `SettingsManager::set()` con `group: 'general'`. Para settings de un módulo nuevo, crea tu propia Filament Page siguiendo el mismo patrón, usando un grupo distinto.
