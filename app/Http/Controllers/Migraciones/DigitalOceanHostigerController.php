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
        $limit = request()->limit;
        $companies = Company::has('logo')->with(['logo' => function ($query) {
            $query->where('migrated', 0);
        }])->orderBy('id', 'desc')->take($limit)->get();

        //Primero voy revisando las compañías
        if (count($companies) > 0) {
            foreach ($companies as $company) {
                $url = $this->decargarImagen(env('DO_URL_BASE') . '/' . $company->logo->url, 'imagenes');
                if ($url) {
                    $url = str_replace('/storage/', '', $url);
                    $company->logo()->delete();
                    $company->logo()->create(["url" => $url, "migrated" => 1]);
                }
            }
            return response()->json(["Logos de empresa procesados" => $limit]);
        } else {
            $products = Product::has('image')->with(['image' => function ($query) {
                $query->where('migrated', 0);
            }])->orderBy('id', 'desc')->take($limit)->get();
            //Primero voy revisando las compañías
            if (count($products) > 0) {
                foreach ($products as $product) {
                    $url = $this->decargarImagen(env('DO_URL_BASE') . '/' . $product->image->url, 'imagenes');
                    if ($url) {
                        $url = str_replace('/storage/', '', $url);
                        $product->image()->delete();
                        $product->image()->create(["url" => $url, "migrated" => 1]);
                    }
                }
                return response()->json(["Imágenes de productos procesados" => $limit]);
            }
            return response()->json(["data" => "listo"]);
        }
    }
}
