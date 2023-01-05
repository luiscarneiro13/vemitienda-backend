<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Images
{
    static function uploadImage($folder)
    {
        return request()->file('image')->storePublicly($folder, 'do');
    }

    static function deleteImage($url)
    {
        Storage::disk('do')->delete($url);
    }

    static function convertUrlToBase64()
    {
        return base64_encode(file_get_contents(request()->file('image')));
    }
}
