<?php

namespace App\Http\Controllers;

use App\Helpers\Images;
use App\Models\Image as ModelsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PruebasController extends Controller
{

    const LIMIT = 5;

    public function index()
    {
        $images = new Images();
        return $images->getFiles();
    }

    public function changeFormat()
    {
        $limit = self::LIMIT;

        if (request()->limit) {
            $limit = request()->limit;
        }

        $data = $this->getAllImagesDB($limit);

        if (count($data) > 0) {
            return $this->startTour($data);
        } else {
            return "Todo Listo";
        }
    }

    public function startTour($arrayData)
    {
        foreach ($arrayData as $item) {

            $imageUrl = env('APP_URL') . '/' . $item->url;
            $imageThumbnail = env('APP_URL') . '/' . $item->url;

            $extensionImage = $this->getExtensionFileFromURL($imageUrl);
            $extensionThumbnail = $this->getExtensionFileFromURL($imageThumbnail);

            if ($extensionImage != 'webp') {
                $item->url = $this->convertImageToWebpAndUploadToS3($imageUrl, 'images');
                $item->migrated = 1;
                $item->save();
                Storage::disk('do')->delete($imageUrl);
            }

            if ($extensionThumbnail != 'webp') {
                $item->thumbnail = $this->convertImageToWebpAndUploadToS3($imageThumbnail, 'thumbnails');
                $item->migrated = 1;
                $item->save();
                Storage::disk('do')->delete($imageThumbnail);
            }
        }
        $falta = ModelsImage::where('migrated', 0)->count();
        return "Faltan: " . $falta . " - Migración Lista: " . time();
    }

    public function getAllImagesDB($limit)
    {
        return ModelsImage::where('migrated', 0)->take($limit)->get();
    }

    public function convertImageToWebpAndUploadToS3($imageUrl, $folder)
    {
        $imageContents = file_get_contents($imageUrl);
        $image = Image::make($imageContents);
        $image->encode('webp');
        $path = $folder . '/' . uniqid() . uniqid() . '.webp';
        Storage::disk('do')->put($path, $image, 'public');
        // Storage::disk('do')->delete($imageUrl);
        return $path;
    }

    public function getExtensionFileFromURL($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return $extension;
    }

    public function velocity(Request $request)
    {
        info(json_encode($request->all()));
    }

    function cleanUpUnusedImages()
    {
        $imagesInDatabase = DB::table('images')->pluck('url')->toArray();
        $thumbnailsInDatabase = DB::table('images')->pluck('thumbnail')->toArray();

        $imagesInServer = File::allFiles(public_path('images'));
        $thumbnailsInServer = File::allFiles(public_path('thumbnails'));

        $imagePathsInServer = array_map(function ($file) {
            return str_replace(public_path(), '', $file->getRealPath());
        }, $imagesInServer);

        $thumbnailPathsInServer = array_map(function ($file) {
            return str_replace(public_path(), '', $file->getRealPath());
        }, $thumbnailsInServer);

        foreach ($imagePathsInServer as $path) {
            if (!in_array($path, $imagesInDatabase)) {
                File::delete(public_path($path));
            }
        }

        foreach ($thumbnailPathsInServer as $path) {
            if (!in_array($path, $thumbnailsInDatabase)) {
                File::delete(public_path($path));
            }
        }
    }
}
