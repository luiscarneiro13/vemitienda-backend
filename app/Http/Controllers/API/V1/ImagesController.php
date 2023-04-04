<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Image;
use App\Models\Product;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImagesController extends Controller
{

    use ApiResponser;

    const STRATEGY = [
        'logo' => LogoImages::class,
        'image' => ImagesImage::class,
    ];

    private $image;

    public function __construct()
    {
        $this->image = new Images();
    }

    public function storeLogo()
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();


        if ($company) {

            try {
                $this->deleteImageProduct($company->logo);
            } catch (Exception $th) {
            }

            try {
                $urlImage = Images::uploadImage('images');
                $thumbnail = Images::uploadThumbnail('thumbnails');
                sleep(3);
                $image = $company->logo()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);
                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
            return $this->errorResponse(['message' => 'No existe la tienda']);
        }
    }

    public function storeImageProduct($product_id)
    {
        $product = Product::with('image')->find($product_id);
        if ($product && request()->image && request()->thumbnail) {
            try {
                $urlImage = Images::uploadImage('images');
                $thumbnail = Images::uploadThumbnail('thumbnails');
                // sleep(3);
                $image = $product->image()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);
                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
        } else {
            return $this->errorResponse(['message' => 'No existe el producto o no se envió imagen']);
        }
    }

    public function updateImageProduct($image_id)
    {
        $image = Image::find($image_id);

        if ($image && request()->image && request()->thumbnail) {
            try {
                $this->deleteImageProduct($image);
            } catch (Exception $th) {
            }

            try {
                $urlImage = Images::uploadImage('images');
                $thumbnail = Images::uploadThumbnail('thumbnails');
                // sleep(3);
                $product = Product::find($image->imageable_id);
                $product->image()->delete();
                $image = $product->image()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);

                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
        } else {
            return $this->errorResponse(['message' => 'La imagen no existe o no se envió una']);
        }
    }

    public function deleteImageProduct($image)
    {
        try {
            $this->image->deleteImage(@$image->url, "do");
            $this->image->deleteImage(@$image->thumbnails, "do");

            $image->delete();

            return $this->successResponse(['data' => $image]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }
}
