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
        $imagen = request()->file('image');
        $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();

        $rutaTemporal = $imagen->getRealPath();

        // Convertir la imagen a formato WebP
        $imagenWebP = Image::make($rutaTemporal)->encode('webp', 80);
        $rutaImagenWebP = 'temp/' . $nombreImagen . '.webp';
        Storage::disk('do')->put($rutaImagenWebP, $imagenWebP->getEncoded());

        // Guardar la imagen en el bucket de DigitalOcean
        $rutaImagenS3 = $folder . '/' . $nombreImagen;
        Storage::disk('do')->put($rutaImagenS3, file_get_contents($rutaTemporal));

        // Eliminar la imagen temporal
        unlink($rutaTemporal);

        return $rutaImagenS3;
    }
    // public function uploadImage($folder)
    // {
    //     $path = request()->file('image')->storePublicly($folder, 'do');
    //     return $path;
    // }

    public function uploadThumbnail($folder)
    {
        $imagen = request()->file('thumbnail');
        $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();

        $rutaTemporal = $imagen->getRealPath();

        // Convertir la imagen a formato WebP
        $imagenWebP = Image::make($rutaTemporal)->encode('webp', 80);
        $rutaImagenWebP = 'temp/' . $nombreImagen . '.webp';
        Storage::disk('do')->put($rutaImagenWebP, $imagenWebP->getEncoded());

        // Guardar la imagen en el bucket de DigitalOcean
        $rutaImagenS3 = $folder . '/' . $nombreImagen;
        Storage::disk('do')->put($rutaImagenS3, file_get_contents($rutaTemporal));

        // Eliminar la imagen temporal
        unlink($rutaTemporal);

        return $rutaImagenS3;
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
