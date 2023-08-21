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
        $path = Storage::disk('do')->put($folder, $image, 'public');

        // Devuelve la URL pública del archivo almacenado
        return Storage::disk('do')->url($path);
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
        $path = Storage::disk('do')->put($folder, $image, 'public');

        // Devuelve la URL pública del archivo almacenado
        return Storage::disk('do')->url($path);
    }


    public function deleteImage($url)
    {
        Storage::disk('do')->delete($url);
    }

    public function convertUrlToBase64()
    {
        return base64_encode(file_get_contents(request()->file('image')));
    }
}
