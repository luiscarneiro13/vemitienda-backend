<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class Images
{
    static function uploadImage($folder)
    {
        info(1);
        $data['url'] = request()->file('image')->storePublicly($folder, 'do');
        info(2);

        info(3);
        /*Thumbnail */
        $file = request()->file('image');
        // info(4);
        $imageName = 'thumbnails/' . Str::random(40) . '.png';
        // info(5);
        $img = Image::make($file);
        // info(6);

        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        });
        info(7);

        $resource = $img->stream()->detach();
        info(8);

        Storage::disk('do')->put($imageName, $resource, 'public');
        info(9);
        $data['thumbnail'] = $imageName;
        return $data;
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
