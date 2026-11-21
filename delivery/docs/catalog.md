# Catálogo público y pedidos por WhatsApp

Capa gratuita de la plataforma: el cliente abre el catálogo público de una sucursal, arma su pedido y la web genera el texto listo para enviarlo al WhatsApp de la tienda. El pedido **no** se guarda en la base de datos.

## Modelo de datos

```
companies 1───N catalogs 1───N catalog_categories 1───N products 1───N product_option_groups 1───N product_options
    │               (is_default)                                        (single|multiple)
    └──N branches ──N── cities

Futuro (NO crear ahora): catalog_branch (catalog_id, branch_id), catalog_city (catalog_id, city_id),
branch_product_overrides (branch_id, product_id, price, is_available)
```

Todas las tablas del catálogo llevan `company_id` (para el aislamiento con `CompanyScope`) **y** su FK al padre.

- `catalogs`: catálogo de una tienda; `is_default` marca el catálogo principal. No hay restricción única por tienda: la regla "un solo catálogo por defecto" se garantiza en código (`EnsureDefaultCatalogAction`).
- `catalog_categories`: categorías dentro de un catálogo, con `sort_order` e `is_active`.
- `products`: productos con `price`, `image_path` (archivo subido) / `image_url` (URL externa https, tiene prioridad), `is_active` (visible) e `is_available` (`false` = "Agotado": se muestra pero no se puede pedir).
- `product_option_groups`: grupos de opciones/extras por producto; `selection_type` es `'single'` o `'multiple'`, `is_required` y `max_selections` (para `single` se fuerza a 1). `is_removal = true` marca grupos de "quitar ingredientes" (ej: *Sin cebolla*): el modelo los fuerza a `multiple`, opcionales y sin límite; sus opciones no suman precio.
- `product_options`: opciones dentro de un grupo, con `price` adicional e `is_active`.

## Catálogo por defecto

Cada tienda tiene un catálogo por defecto que se crea automáticamente:

- Al crear una `Company` se dispara el evento `CompanyCreated`, que ejecuta `App\Domain\Catalog\Listeners\CreateDefaultCatalogForCompany` → `EnsureDefaultCatalogAction`.
- `EnsureDefaultCatalogAction::execute(Company $company)` es idempotente: si ya existe un catálogo `is_default` para la tienda, lo devuelve; si no, lo crea.
- Para tiendas creadas antes de esta funcionalidad (o si el listener falla), existe el comando:

  ```bash
  docker compose exec delivery-app php artisan catalog:ensure-defaults
  ```

  Recorre todas las tiendas y les asegura su catálogo por defecto.

## Enlace público

Formato: `https://dominio.com/c/{slug_de_la_tienda}/{uuid_de_la_sucursal}`

- El UUID de la sucursal va en la ruta (no en query param) para que el cliente no pueda cambiar de sucursal probando valores.
- El catálogo que se muestra lo decide `App\Domain\Catalog\Services\CatalogResolver::forBranch($branch)`. Hoy devuelve el catálogo por defecto activo de la tienda.
- Rate limit: `throttle:public-catalog` (60 peticiones/minuto por IP). Todas las respuestas llevan `X-Robots-Tag: noindex, nofollow`.

Casos que devuelven la página de error (HTTP 404, mismo mensaje para todos, sin revelar la causa):

- URL sin UUID, UUID con formato inválido o segmentos extra.
- Slug de tienda inexistente.
- Tienda inactiva (`is_active = false` o `status != 'active'`) o eliminada (soft delete).
- UUID que no existe, es de otra tienda, o la sucursal está inactiva.
- Sucursal sin `whatsapp_number` (no hay a dónde enviar el pedido).
- La tienda no tiene catálogo por defecto activo.

El controlador es `App\Http\Controllers\PublicCatalog\PublicCatalogController` y el payload lo arma `App\Domain\Catalog\Services\PublicCatalogPresenter`: solo envía datos públicos (nada de `company_id`, usuarios, emails ni teléfonos ajenos), precios en centavos enteros y URLs de imágenes.

## Formato del mensaje de WhatsApp

El carrito vive en el navegador (`localStorage`, una clave por UUID de sucursal) y no se envía al servidor. Al pulsar "Enviar pedido por WhatsApp" se abre:

```
https://wa.me/{whatsapp_de_la_sucursal}?text={mensaje_url_encodeado}
```

Formato del mensaje (`buildMessage()` en `resources/js/public-catalog.js`):

```
*Nuevo pedido* — {nombre de la tienda}
Sucursal: {nombre de la sucursal} — {ciudad}

1) {cantidad} x {producto} — {precio línea}
   • {Grupo}: {Opción} (+{precio extra}), ...
   • Sin: {ingredientes quitados}        ← grupos is_removal
   Nota: {nota del ítem}
2) ...

*Total estimado: {total}*

Nota general: {nota general}

_Pedido generado desde el catálogo web. La tienda confirmará el monto final._
```

Reglas del carrito: productos agotados no se pueden agregar; grupos obligatorios deben tener selección; grupos `single` admiten una opción; grupos `multiple` respetan `max_selections`; ítems idénticos (producto + opciones + nota) suman cantidad; el carrito **no** se vacía automáticamente al enviar.

## Extensión futura

Los catálogos por sucursal o por ciudad se agregarán **sin modificar las tablas existentes**:

- Nuevas tablas pivote: `catalog_branch (catalog_id, branch_id)` y `catalog_city (catalog_id, city_id)`.
- Si hacen falta precios o disponibilidad distintos por sucursal: tabla nueva de overrides, p. ej. `branch_product_overrides (branch_id, product_id, price, is_available)`.
- Solo se modifica `CatalogResolver::forBranch()`: hoy devuelve el catálogo por defecto de la tienda; en el futuro resolverá en orden sucursal → ciudad → tienda.
- Todo lo demás (presentador, controlador, vistas) ya filtra por el catálogo que devuelve el resolver.

## Roles

- `super_admin`: acceso total, gestiona tiendas, sucursales y ciudades.
- `admin`: permisos del starter, **sin** permisos del catálogo ni de ciudades.
- `store_owner` ("Dueño de tienda"): ve y actualiza solo los datos de su tienda.
  - Puede: ver/actualizar su empresa, ver/actualizar sus sucursales (sin crear ni borrar), ver/actualizar su catálogo, crear/editar/reordenar categorías y productos (con sus grupos de opciones).
  - No puede: crear catálogos ni sucursales, borrar sucursales, gestionar ciudades ni ver datos de otras tiendas.
