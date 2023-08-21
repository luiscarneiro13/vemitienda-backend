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

        // Guardar la imagen original
        $rutaTemporal = $imagen->storePublicly('temp', 'public');

        // Convertir la imagen a formato WebP utilizando Intervention Image
        $imagenWebP = Image::make(public_path('storage/' . $rutaTemporal))->encode('webp', 75);
        $rutaImagenWebP = 'temp/' . $nombreImagen . '.webp';
        Storage::disk('public')->put($rutaImagenWebP, $imagenWebP->getEncoded());

        // Eliminar la imagen original
        Storage::disk('public')->delete($rutaTemporal);

        // Guardar la imagen en el bucket de DigitalOcean
        $rutaImagenS3 = $folder . '/' . $nombreImagen;
        Storage::disk('do')->put($rutaImagenS3, file_get_contents(public_path('storage/' . $rutaImagenWebP)));

        // Eliminar la imagen en formato WebP temporal
        Storage::disk('public')->delete($rutaImagenWebP);

        //Se asigna permiso público para que la imagen se pueda ver
        Storage::disk('do')->setVisibility($rutaImagenS3, 'public');

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

        // Guardar la imagen original
        $rutaTemporal = $imagen->storePublicly('temp', 'public');

        // Convertir la imagen a formato WebP utilizando Intervention Image
        $imagenWebP = Image::make(public_path('storage/' . $rutaTemporal))->encode('webp', 75);
        $rutaImagenWebP = 'temp/' . $nombreImagen . '.webp';
        Storage::disk('public')->put($rutaImagenWebP, $imagenWebP->getEncoded());

        // Eliminar la imagen original
        Storage::disk('public')->delete($rutaTemporal);

        // Guardar la imagen en el bucket de DigitalOcean
        $rutaImagenS3 = $folder . '/' . $nombreImagen;
        Storage::disk('do')->put($rutaImagenS3, file_get_contents(public_path('storage/' . $rutaImagenWebP)));

        // Eliminar la imagen en formato WebP temporal
        Storage::disk('public')->delete($rutaImagenWebP);

        //Se asigna permiso público para que la imagen se pueda ver
        Storage::disk('do')->setVisibility($rutaImagenS3, 'public');

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
