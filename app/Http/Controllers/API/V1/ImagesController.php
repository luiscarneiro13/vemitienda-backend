<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Image;
use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImagesController extends Controller
{

    use ApiResponser;

    const STRATEGY = [
        'logo' => LogoImages::class,
        'image' => ImagesImage::class,
    ];

    public function storeLogo()
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();
        $urlLogo = Images::uploadImage(request->folder);

        try {
            $company->logo()->delete();
        } catch (\Throwable $th) {
        }

        try {
            $company->logo()->create(['url' => $urlLogo]);
            return $this->successResponse(['data' => $company]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    public function storeImageProduct($product_id)
    {
        try {
            $product = Product::find($product_id);
            $urlImage = Images::uploadImage(request->folder);
            $product->image()->create(['url' => $urlImage]);
            return $this->successResponse(['data' => $product]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    // public function logo()
    // {
    //     $user = Auth::user();
    //     $company = Company::where('user_id', $user->id)->first();
    //     /*
    //     variables:
    //     @image,@model
    //      */
    //     if (request()->logo) {
    //         try {
    //             $this->image->deleteImage(@$company->logo->url, "do");
    //             $company->Logo()->delete();
    //         } catch (\Throwable $th) {
    //         }

    //         try {
    //             $urlLogo = $this->image->uploadImage('logo', 'logos', 'do');
    //             $company->logo()->create(['url' => $urlLogo]);
    //             return $this->successResponse(['data' => $company]);
    //         } catch (\Throwable $th) {
    //             return $this->errorResponse(['message' => $th]);
    //         }
    //     }
    // }

    // public function image()
    // {
    //     $product = Product::find(request()->product_id);

    //     if (request()->image) {
    //         try {
    //             $this->image->deleteImage(@$product->image->url, "do");
    //             $product->image()->delete();
    //         } catch (\Throwable $th) {
    //         }

    //         try {
    //             $urlProduct = $this->image->uploadImage('image', 'products', 'do');
    //             $product->image()->create(['url' => $urlProduct]);
    //             return $this->successResponse(['data' => $product]);
    //         } catch (\Throwable $th) {
    //             return $this->errorResponse(['message' => $th]);
    //         }
    //     }
    //     return "Image";
    // }

    // public function imageDelete()
    // {
    //     $image = Image::find(request()->image_id);

    //     try {
    //         $this->image->deleteImage(@$image->url, "do");
    //         $image->delete();
    //         return $this->successResponse(['data' => $image]);
    //     } catch (\Throwable $th) {
    //         return $this->errorResponse(['message' => $th]);
    //     }
    // }
}
