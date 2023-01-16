<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class Images
{
    static function uploadImage($folder)
    {
        return request()->file('image')->storePublicly($folder, 'do');
    }

    static function uploadThumbnail($folder)
    {
        $folder = 'thumbnails';
        $file = request()->file('image');
        $imageName = Str::random(40);
        $img = Image::make($file);

        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        });

        //detach method is the key! Hours to find it... :/
        $resource = $img->stream()->detach();

        Storage::disk('do')->put($folder . '/' . $imageName, $resource, 'public');

        return $folder . '/' . $imageName;
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
