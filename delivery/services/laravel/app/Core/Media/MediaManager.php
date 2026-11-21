<?php

namespace App\Core\Media;

use App\Core\Media\Contracts\MediaDriverInterface;
use App\Core\Media\Models\Media;
use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Punto único de gestión de archivos (logos, documentos, imágenes,
 * adjuntos). Delega el almacenamiento físico en el driver activo
 * (Strategy Pattern) — cambiar de Local a S3/Minio/Azure es implementar
 * MediaDriverInterface y cambiar `core.media.driver`, sin tocar el
 * resto de la aplicación.
 */
class MediaManager
{
    public function __construct(
        private readonly MediaDriverInterface $driver,
        private readonly TenantContext $tenant,
    ) {}

    public function attach(Model $model, UploadedFile $file, string $collection = 'default'): Media
    {
        abort_unless(Media::canManageSubject($model), 403, 'No tienes permiso sobre archivos de otra empresa.');

        $stored = $this->driver->store($file, $collection);

        return Media::create([
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
            'collection' => $collection,
            'created_by' => $this->tenant->userId(),
            ...$stored,
        ]);
    }

    public function delete(Media $media): bool
    {
        abort_unless($media->isOwnedByCurrentTenant(), 403, 'No tienes permiso sobre archivos de otra empresa.');

        $this->driver->delete($media->disk, $media->path);

        return (bool) $media->delete();
    }

    public function url(Media $media): string
    {
        return $this->driver->url($media->disk, $media->path);
    }

    /**
     * @return Collection<int, Media>
     */
    public function for(Model $model, ?string $collection = null): Collection
    {
        return Media::query()
            ->where('model_type', $model->getMorphClass())
            ->where('model_id', $model->getKey())
            ->when($collection, fn ($query) => $query->where('collection', $collection))
            ->get();
    }
}
