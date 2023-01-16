<?php

namespace App\Helpers;

use Exception;
use Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Images
{
    static function uploadImage($folder)
    {
        $data['url'] = request()->file('image')->storePublicly($folder, 'do');

        try {

            /*Thumbnail */

            $imageName = 'thumbnails/' . Str::random(40) . '.png';
            $img = Image::make(request()->file('image'));

            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            });

            $resource = $img->stream()->detach();

            Storage::disk('do')->put($imageName, $resource, 'public');
            $data['thumbnail'] = $imageName;

        } catch (Exception $th) {
            $data['thumbnail'] = "NEI";
        }
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
