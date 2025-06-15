<?php

namespace App\Http\Controllers\Migraciones;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;

class DigitalOceanHostigerController extends Controller
{
    public $company, $product, $images;

    public function __construct()
    {
        $this->company = new Company();
        $this->product = new Product();
        $this->images = new Image();
    }

    public function decargarImagen($url, $carpeta)
    {
        $client = new Client();
        try {
            $response = $client->get($url);
            $fileName = uniqid() . '.webp'; // Define el nombre que deseas darle al archivo
            $filePath = $carpeta . '/' . $fileName; // Define la ruta donde deseas almacenar el archivo
            Storage::disk('public')->put($filePath, $response->getBody());
            $url = Storage::url($filePath);
            $url = str_replace('/storage/', '', $url);
            return $url;
        } catch (\Throwable $th) {
            try {
                $response = $client->get(str_replace('/images/', '/thumbnails/', $url));
                $fileName = uniqid() . '.webp'; // Define el nombre que deseas darle al archivo
                $filePath = $carpeta . '/' . $fileName; // Define la ruta donde deseas almacenar el archivo
                Storage::disk('public')->put($filePath, $response->getBody());
                $url = Storage::url($filePath);
                $url = str_replace('/storage/', '', $url);
            } catch (\Throwable $th) {
                return response()->json(["error" => $th]);
            }
        }
    }

    public function migrarImagenes()
    {
        $model = '';
        switch (request()->type) {
            case 'companies':
                $model = 'App\Models\Company';
                break;

            case 'products':
                $model = 'App\Models\Product';
                break;

            default:
                # code...
                break;
        }

        return $this->imagesProccess($model);

    }

    public function imagesProccess($model)
    {
        $limit = request()->limit;
        $images = $this->images->where('imageable_type', $model)->orderBy('id', 'desc')->take($limit)->get();
        $result = [];
        if (count($images) > 0) {
            foreach ($images as $item) {
                if (isset($item->url)) {
                    $result[] = env('DO_URL_BASE') . '/' . $item->url;
                    $logo = Image::find($item->id);
                    $logo->migrated = 1;
                    $logo->save();
                }
            }
        }
        return $result;
    }
}
