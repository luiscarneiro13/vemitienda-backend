<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Images
{
    public function uploadImage($input, $folder, $disk)
    {
        return request()->file($input)->storePublicly($folder, $disk);
    }

    public function deleteImage($user, $disk, $folder)
    {
        Storage::disk($disk)->delete($user->Imagen->url);
    }
}
