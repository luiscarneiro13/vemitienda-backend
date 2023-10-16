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
        $companiesArray = Company::has('logo')->orderBy('id', 'desc')->take($limit)->pluck('id');
        $companies = Company::with(['logo' => function ($q) {
            $q->where('migrated', 0);
        }])->whereIn('id', $companiesArray)->take($limit)->get();
        // return $companies;
        if (count($companies) > 0) {
            $proceced = [];
            foreach ($companies as $company) {
                $url = $this->decargarImagen(env('DO_URL_BASE') . '/' . $company->logo->url, 'images');
                if ($url) {
                    $url = str_replace('/storage/', '', $url);
                    $company->logo()->delete();
                    $id = $company->logo()->create(["url" => $url, "migrated" => 1]);
                    $proceced[] = $id;
                }
            }
            return response()->json(["Logos de empresa procesados" => $proceced]);
        } else {

            $productsArray = Product::has('image')->orderBy('id', 'desc')->take($limit)->pluck('id');
            $products = Product::with(['image' => function ($q) {
                $q->where('migrated', 0);
            }])->whereIn('id', $productsArray)->take($limit)->get();

            //Primero voy revisando las compañías
            if (count($products) > 0) {
                $proceced = [];
                foreach ($products as $product) {
                    $url = $this->decargarImagen(env('DO_URL_BASE') . '/' . $product->image->url, 'images');
                    if ($url) {
                        $url = str_replace('/storage/', '', $url);
                        $product->image()->delete();
                        $product->image()->create(["url" => $url, "migrated" => 1]);
                        $proceced[] = $product->id;
                    }
                }
                return response()->json(["Imágenes de productos procesados" => $proceced]);
            }
            return response()->json(["data" => "listo"]);
        }
    }
}
