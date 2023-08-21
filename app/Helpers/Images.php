<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Images
{
    public function uploadImage($folder)
    {
        // Paso 1: Recibe un archivo llamado "image" de tipo "png"
        $file = request()->file('image');

        // Paso 2: Convierte el archivo a formato webp
        $image = Image::make($file)->encode('webp');

        // Paso 3: Almacena el archivo en S3 con permisos públicos
        return Storage::disk('do')->put($folder, $image, 'public');
    }
    // public function uploadImage($folder)
    // {
    //     $path = request()->file('image')->storePublicly($folder, 'do');
    //     return $path;
    // }

    public function uploadThumbnail($folder)
    {
        // Paso 1: Recibe un archivo llamado "image" de tipo "png"
        $file = request()->file('thumbnail');

        // Paso 2: Convierte el archivo a formato webp
        $image = Image::make($file)->encode('webp');

        // Paso 3: Almacena el archivo en S3 con permisos públicos
        return Storage::disk('do')->put($folder, $image, 'public');
    }

    public function deleteImage($url)
    {
        Storage::disk('do')->delete($url);
    }

    public function convertUrlToBase64()
    {
        return base64_encode(file_get_contents(request()->file('image')));
    }

    public function getImagesFromS3Folder()
    {
        $folder = 'nombre_de_la_carpeta'; // Reemplaza 'nombre_de_la_carpeta' por el nombre de tu carpeta en S3

        $files = Storage::disk('s3')->files($folder);

        $images = [];

        foreach ($files as $file) {
            if (Storage::disk('s3')->mimeType($file) == 'image/jpeg' || Storage::disk('s3')->mimeType($file) == 'image/png') {
                $images[] = Storage::disk('s3')->url($file);
            }
        }

        return $images;
    }
}
