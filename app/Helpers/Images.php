<?php

namespace App\Helpers;

use App\Models\Image;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as FacadeImage;

class Images
{
    public function uploadImage($imagen, $folder)
    {
        $image = FacadeImage::make($imagen);
        $image->encode('webp');
        $nombreImagen = $folder . '/' . uniqid() . uniqid() . '.webp';
        Storage::disk('do')->put($nombreImagen, $image->stream(), 'public');
        return $nombreImagen;
    }

    public function uploadImagePng($imagen, $folder)
    {
        $image = FacadeImage::make($imagen);
        $image->encode('png');
        $nombreImagen = $folder . '/' . uniqid() . uniqid() . '.png';
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

    public function getFiles()
    {
        $images = Storage::disk('do')->files('thumbnails');
        //Regresa un array que contiene ["images/064EsMWPMR1G4f4G7trNAmNhs4OMppPQj6BYHm8K.png","images/064EsMWPMR1G4f4G7trNAmNhs4OMppPQj6BYHm8K.png"]
        $noExists = 0;

        $imagesDB = Image::pluck('thumbnail')->toArray();

        $dif = array_diff($images, $imagesDB);

        foreach ($dif as $value) {
            $noExists++;
            // return $value;
            // $this->deleteImage($value, "do");
        }

        return $noExists;
    }
}
