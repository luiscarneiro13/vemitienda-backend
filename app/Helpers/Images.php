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
        info(1);
        // Paso 1: Recibe un file llamado image y de tipo png
        $imagen = request()->file('image');
        info(2);

        // Paso 2: Convierte el file en webp
        $imagenWebp = Image::make($imagen)->encode('webp');
        info(3);

        // Paso 3: Almacénalo en DigitalOcean con storePublicly
        $rutaImagen = $imagenWebp->storePublicly($folder, 'do');
        info(4);

        return $rutaImagen;
    }
    // public function uploadImage($folder)
    // {
    //     $path = request()->file('image')->storePublicly($folder, 'do');
    //     return $path;
    // }

    public function uploadThumbnail($folder)
    {
        // Paso 1: Recibe un file llamado image y de tipo png
        $imagen = request()->file('thumbnail');

        // Paso 2: Convierte el file en webp
        $imagenWebp = Image::make($imagen)->encode('webp');

        // Paso 3: Almacénalo en DigitalOcean con storePublicly
        $rutaImagen = $imagenWebp->storePublicly($folder, 'do');

        return $rutaImagen;
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
