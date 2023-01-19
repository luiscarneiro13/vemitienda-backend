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
     *                 @OA\Property(
     *                     description="Miniatura",
     *                     property="thumbnail",
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
        info(1);
        $user = Auth::user();
        info(2);
        $company = Company::where('user_id', $user->id)->first();
        info(3);

        if ($company) {
            info(4);
            try {
                info(5);
                $urlImage = Images::uploadImage('images');
                info(6);
                $thumbnail = Images::uploadThumbnail('thumbnails');
                info(7);
                sleep(3);
                info(8);
                $image = $company->logo()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);
                info(9);
                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                info(10);
                return $this->errorResponse(['message' => $th]);
            }
            info(11);
        } else {
            info(12);
            return $this->errorResponse(['message' => 'No existe la tienda']);
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
     *                 @OA\Property(
     *                     description="Miniatura",
     *                     property="thumbnail",
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
        $product = Product::with('image')->find($product_id);
        if ($product) {

            try {
                $this->deleteImageProduct($product->image);
            } catch (Exception $th) {
            }

            try {
                $urlImage = Images::uploadImage('images');
                $thumbnail = Images::uploadThumbnail('thumbnails');
                sleep(3);
                $image = $product->image()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);
                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
        } else {
            return $this->errorResponse(['message' => 'No existe el producto']);
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
     *                 @OA\Property(
     *                     description="Miniatura",
     *                     property="thumbnail",
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
        $image = Image::find($image_id);

        if ($image) {

            try {
                $this->deleteImageProduct($image);
            } catch (Exception $th) {
            }

            try {
                $urlImage = Images::uploadImage('images');
                $thumbnail = Images::uploadThumbnail('thumbnails');
                sleep(3);
                $product = Product::find($image->imageable_id);
                $product->image()->delete();
                $image = $product->image()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);

                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
        } else {
            return $this->errorResponse(['message' => 'La imagen no existe']);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Products"},
     *     path="/deleteImageProduct/{image_id}",
     *     security={{"bearer_token":{}}},
     *     summary="Borrar imagen de un Producto",
     *      @OA\Parameter(
     *          name="image_id",
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
