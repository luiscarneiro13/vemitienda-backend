<?php

namespace App\Http\Controllers\API\V3;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Image;
use App\Models\Post;
use App\Models\Product;
use App\Traits\ApiResponser;
use App\User;
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
     *     path="/v3/storeLogo",
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
    public function storeLogo(Request $request)
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();


        if ($company) {

            try {
                $this->deleteImageProduct($company->logo);
            } catch (Exception $th) {
            }

            try {
                $urlImage = $this->image->uploadImage($request->file('image'), 'images');
                $thumbnail = $this->image->uploadImage($request->file('thumbnail'), 'thumbnails');
                // sleep(3);
                $image = $company->logo()->create(['url' => $urlImage, 'thumbnail' => $thumbnail]);
                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
            return $this->errorResponse(['message' => 'No existe la tienda']);
        }
    }


    /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/v3/storeImageProduct/{product_id}",
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

    public function storeImageProduct(Request $request, $product_id)
    {
        $product = Product::with('image')->has('image')->find($product_id);
        if ($product && request()->image && request()->thumbnail) {
            try {
                $urlImage = $this->image->uploadImage($request->file('image'), 'images');
                $image = $product->image()->create(['url' => $urlImage]);
                return $this->successResponse(['data' => $image]);
            } catch (Exception $th) {
                return $this->errorResponse(['message' => $th]);
            }
        } else {
            return $this->errorResponse(['message' => 'No existe el producto o no se envió imagen']);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/v3/updateImageProduct/{image_id}",
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
    public function updateImageProduct(Request $request, $image_id)
    {
        $image = Image::find($image_id);

        if ($image && request()->image && request()->thumbnail) {
            try {
                $this->deleteImageProduct($image);
            } catch (Exception $th) {
            }

            try {
                $urlImage = $this->image->uploadImage($request->file('image'), 'images');
                $thumbnail = $this->image->uploadImage($request->file('thumbnail'), 'thumbnails');
                // sleep(3);
                $product = Product::has('image')->find($image->imageable_id);
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

    /**
     * @OA\Delete(
     *     tags={"Products"},
     *     path="/v3/deleteImageProduct/{image_id}",
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

    /**
     * @OA\Post(
     *     tags={"Posts"},
     *     path="/v3/admin/storeImagePost",
     *     security={{"bearer_token":{}}},
     *     summary="Cargar imágenes de un post",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Folder de la imagen",
     *                     property="folder",
     *                     type="string",
     *                     format="text",
     *                     default="posts",
     *                 ),
     *                 @OA\Property(
     *                     description="subir imagen",
     *                     property="upload",
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
    public function storeImagePost($image, $id)
    {
        $post = Post::find($id);
        $post->image()->delete();
        $urlImage = $this->image->uploadImage($image, 'images');
        $image = $post->image()->create(['url' => $urlImage, 'thumbnail' => $urlImage]);
        return env('APP_URL') . '/' . $image->url;
    }
}
