<?php

namespace App\Http\Controllers;

use App\Helpers\Images;
use App\Models\Image as ModelsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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

            $imageUrl = env('DO_URL_BASE') . '/' . $item->url;
            $imageThumbnail = env('DO_URL_BASE') . '/' . $item->thumbnail;

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
        return "Migración Lista: ".time();
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
}
