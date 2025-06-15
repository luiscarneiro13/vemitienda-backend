<?php

namespace App\Http\Controllers;

use App\Helpers\Images;
use App\Models\Image as ModelsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Product;

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

    //Para hacer limpieza de las imágenes que no se están usando
    function cleanUpUnusedImages()
    {
        $this->deleteStep1(); // Borrar las imagenes que están en el server y no en la tabla images
        $this->deleteStep2(); // Borrar imagenes de productos que no existen
        $this->deleteStep3(); // Borrar los productos que no tienen imagen
    }

    public function deleteStep1()
    {
        $imagesInDatabase = DB::table('images')->pluck('url')->toArray();
        $thumbnailsInDatabase = DB::table('images')->pluck('thumbnail')->toArray();
        $imagesInServer = File::allFiles(public_path('images'));
        $thumbnailsInServer = File::allFiles(public_path('thumbnails'));
        $imagePathsInServer = array_map(function ($file) {
            return 'images/' . $file->getFilename();
        }, $imagesInServer);
        $thumbnailPathsInServer = array_map(function ($file) {
            return 'thumbnails/' . $file->getFilename();
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

    public function deleteStep2()
    {
        $images = DB::table('images')->where('imageable_type', 'App\Models\Product')->get();

        foreach ($images as $image) {
            $productExists = Product::find($image->imageable_id);

            if (!$productExists) {
                // Eliminar la imagen de la tabla 'images'
                DB::table('images')->where('id', $image->id)->delete();

                // Eliminar la imagen del servidor
                $imagePath = public_path($image->url);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }

                // Eliminar la miniatura del servidor
                $thumbnailPath = public_path($image->thumbnail);
                if (File::exists($thumbnailPath)) {
                    File::delete($thumbnailPath);
                }
            }
        }
    }

    public function deleteStep3()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $imageCount = DB::table('images')->where('imageable_type', 'App\Models\Product')->where('imageable_id', $product->id)->count();
            if ($imageCount === 0) {
                $product->delete();
            }
        }
    }

    public function offer(Request $request)
    {
        $context1="https://purl.imsglobal.org/spec/ob/v3p0/context-3.0.3.json";
        $context2="https://purl.imsglobal.org/spec/ob/v3p0/context/ob_v3p0.jsonld";

        $response = [
            "offers" => [
                (object)[
                    "type" => [
                        "OpenBadgeCredential"
                    ],
                    "credentialSubject" => (object)[
                        "vendorUserId" => request()->vendorUserId,
                        "type" => [
                            "AchievementSubject"
                        ],
                        "achievement" => (object)[
                            "id" => "https://life.territorium.com/issuedBadge?id=09224cda-c550-45f8-a895-8de8615a6391",
                            "type" => "Achievement",
                            "achievementType" => "Achievement",
                            "name" => "DEMO-Velocity: GVSU Undergraduate Course Credential",
                            "description" => "This is the course achievement description.",
                            "criteria" => (object)[
                                "type" => "Criteria",
                                "narrative" => "Successful Course Completion"
                            ],
                            "image" => (object)[
                                "id" => "https://territorio.s3.amazonaws.com/converted/e3635b8c-1b37-11f0-ad73-0242ac150002.png",
                                "type" => "Image"
                            ]
                        ]
                    ],
                    "offerId" => "09224cda-c550-45f8-a895-8de8615a6392",
                    "exchangeId" => request()->exchangeId,
                ]
            ]
        ];

        return response()->json($response, 200);
    }
}
