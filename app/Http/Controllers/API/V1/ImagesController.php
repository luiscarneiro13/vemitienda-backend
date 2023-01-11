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

    private $image;

    public function __construct()
    {
        $this->image = new Images();
    }

    /**
     * @OA\Post(
     *     tags={"Logo"},
     *     path="/storeLogo",
     *     security={{"bearer_token":{}}},
     *     summary="Crear logo de Empresa",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Folder de la imagen",
     *                     property="folder",
     *                     type="string",
     *                     format="text",
     *                     default="logo",
     *                 ),
     *                 @OA\Property(
     *                     description="subir imagen",
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function storeLogo()
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();
        $urlLogo = Images::uploadImage(request()->folder);

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


    /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/storeImageProduct/{product_id}",
     *     security={{"bearer_token":{}}},
     *     summary="Crear logo de Empresa",
     *     @OA\Parameter(
     *        name="product_id",
     *        in="path",
     *        required=true
     *     ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Folder de la imagen",
     *                     property="folder",
     *                     type="string",
     *                     format="text",
     *                     default="images",
     *                 ),
     *                 @OA\Property(
     *                     description="subir imagen",
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */

    public function storeImageProduct($product_id)
    {
        try {
            $product = Product::find($product_id);
            $urlImage = Images::uploadImage(request()->folder);
            $product->image()->create(['url' => $urlImage]);
            return $this->successResponse(['data' => $product]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/updateImageProduct/{image_id}",
     *     security={{"bearer_token":{}}},
     *     summary="Crear logo de Empresa",
     *     @OA\Parameter(
     *        name="image_id",
     *        in="path",
     *        required=true
     *     ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Folder de la imagen",
     *                     property="folder",
     *                     type="string",
     *                     format="text",
     *                     default="images",
     *                 ),
     *                 @OA\Property(
     *                     description="subir imagen",
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function updateImageProduct($image_id)
    {
        try {
            $image = Image::find($image_id);
            $product_id = $image->imageable_id;
            $this->deleteImageProduct($image_id);
            return $this->storeImageProduct($product_id);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Products"},
     *     path="/deleteImageProduct/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Borrar imagen de un Producto",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function deleteImageProduct($image_id)
    {
        $image = Image::find($image_id);

        try {
            $this->image->deleteImage(@$image->url, "do");
            $image->delete();
            return $this->successResponse(['data' => $image]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }
}
