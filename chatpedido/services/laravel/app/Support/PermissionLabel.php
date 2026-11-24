<?php

namespace App\Support;

/**
 * Traduce el nombre técnico de un permiso (usado por Spatie Permission,
 * Filament Shield y las policies) a un label legible en español para la
 * interfaz. No modifica el dato real almacenado — ver docs/permissions.md
 * para el detalle de los dos formatos que conviven en el sistema.
 */
class PermissionLabel
{
    private const ACTIONS = [
        'ViewAny' => 'Ver todos',
        'View' => 'Ver',
        'Create' => 'Crear',
        'Update' => 'Editar',
        'Delete' => 'Eliminar',
        'DeleteAny' => 'Eliminar todos',
        'Restore' => 'Restaurar',
        'RestoreAny' => 'Restaurar todos',
        'ForceDelete' => 'Eliminar permanentemente',
        'ForceDeleteAny' => 'Eliminar todos permanentemente',
        'Replicate' => 'Duplicar',
        'Reorder' => 'Reordenar',
        'view' => 'Ver',
        'create' => 'Crear',
        'update' => 'Editar',
        'delete' => 'Eliminar',
    ];

    private const MODELS = [
        'Company' => 'Empresa',
        'Branch' => 'Sucursal',
        'User' => 'Usuario',
        'Role' => 'Rol',
        'Permission' => 'Permiso',
        'Plan' => 'Plan',
        'Subscription' => 'Suscripción',
        'Audit' => 'Auditoría',
        'DatabaseNotification' => 'Notificación',
        'Module' => 'Módulo',
        'CompanyModule' => 'Feature Flag',
        'TimelineEntry' => 'Timeline',
        'Media' => 'Archivo',
        'users' => 'Usuarios',
        'roles' => 'Roles',
        'permissions' => 'Permisos',
        'companies' => 'Empresas',
        'settings' => 'Configuración',
    ];

    /**
     * Acepta ambos formatos que conviven en el sistema: "Accion:Modelo"
     * (generado por Filament Shield) y "recurso.accion" (permisos de
     * negocio, ver docs/permissions.md).
     */
    public static function for(string $permission): string
    {
        if (str_contains($permission, ':')) {
            [$action, $model] = explode(':', $permission, 2);

            return (self::ACTIONS[$action] ?? $action).': '.(self::MODELS[$model] ?? $model);
        }

        if (str_contains($permission, '.')) {
            [$resource, $action] = explode('.', $permission, 2);

            return (self::ACTIONS[$action] ?? $action).': '.(self::MODELS[$resource] ?? $resource);
        }

        return $permission;
    }
}
