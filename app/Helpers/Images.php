<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Images
{
    public function uploadImage($imagen, $folder)
    {
        // Crear una instancia de Intervention Image con la imagen cargada
        $image = Image::make($imagen);

        // Convertir la imagen a formato WebP
        $image->encode('webp');

        // Generar un nombre único para la imagen
        $nombreImagen = $folder . '/' . uniqid() . '.webp';

        // Subir la imagen a Amazon DO
        Storage::disk('do')->put($nombreImagen, $image->stream(), 'public');

        // Devolver la URL de la imagen en S3
        return $nombreImagen;
    }
    // public function uploadImage($folder)
    // {
    //     $path = request()->file('image')->storePublicly($folder, 'do');
    //     return $path;
    // }

    public function uploadThumbnail($imagen, $folder)
    {
        // Crear una instancia de Intervention Image con la imagen cargada
        $image = Image::make($imagen);

        // Convertir la imagen a formato WebP
        $image->encode('webp');

        // Generar un nombre único para la imagen
        $nombreImagen = $folder . '/' . uniqid() . '.webp';

        // Subir la imagen a Amazon DO
        Storage::disk('do')->put($nombreImagen, $image->stream(), 'public');

        // Devolver la URL de la imagen en S3
        return $nombreImagen;
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
