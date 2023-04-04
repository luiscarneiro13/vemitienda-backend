<?php

namespace App\Helpers;

use Exception;
use Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Images
{
    public function uploadImage($folder)
    {
        $path = request()->file('image')->storePublicly($folder, 'do');
        return $path;
    }

    public function uploadThumbnail($folder)
    {
        $path = request()->file('thumbnail')->storePublicly($folder, 'do');
        return $path;
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
