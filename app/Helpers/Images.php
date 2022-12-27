<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Images
{
    public function uploadImage($input, $folder, $disk)
    {
        $image = request()->file($input)->storePublicly($folder, $disk);
        return $image;
    }

    public function deleteImage($url, $disk)
    {
        Storage::disk($disk)->delete($url);
    }

    public function convertUrlToBase64($input)
    {
        return base64_encode(file_get_contents(request()->file($input)));
    }
}
