<?php

namespace App\Core\Media\Drivers;

use App\Core\Media\Contracts\MediaDriverInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalMediaDriver implements MediaDriverInterface
{
    public function __construct(private readonly string $disk) {}

    public function store(UploadedFile $file, string $collection): array
    {
        $name = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($collection, $name, $this->disk);

        return [
            'disk' => $this->disk,
            'path' => $path,
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];
    }

    public function delete(string $disk, string $path): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    public function url(string $disk, string $path): string
    {
        return Storage::disk($disk)->url($path);
    }
}
