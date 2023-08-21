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
        $image = Image::make($imagen);
        $image->encode('webp');
        $nombreImagen = $folder . '/' . uniqid() . uniqid() . '.webp';
        Storage::disk('do')->put($nombreImagen, $image->stream(), 'public');
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
}
