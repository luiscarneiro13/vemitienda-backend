<?php

namespace App\Core\Media\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Estrategia de almacenamiento físico de archivos. MediaManager delega
 * en la implementación activa (ver config/core.php: media.driver) — el
 * resto de la aplicación nunca habla directamente con Storage.
 */
interface MediaDriverInterface
{
    /**
     * Persiste el archivo físico y devuelve los metadatos a guardar en
     * la tabla `media`: disk, path, name, original_name, mime_type, size.
     *
     * @return array{disk: string, path: string, name: string, original_name: string, mime_type: ?string, size: int}
     */
    public function store(UploadedFile $file, string $collection): array;

    public function delete(string $disk, string $path): bool;

    public function url(string $disk, string $path): string;
}
