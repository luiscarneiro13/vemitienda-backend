<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImagesController extends Controller
{

    public function __construct()
    {
        $this->image = new Images();
    }

    // if (request()->logo) {

    //     if (@$company->logo->url) {
    //         $this->image->deleteImage($company->logo->url, "do");
    //         $company->Logo()->delete();
    //     }

    //     $urlLogo = $this->image->uploadImage('logo', 'logos', 'do');
    //     $company->logo()->create(['url' => $urlLogo]);
    // }
    public function logo()
    {
        $user = Auth::user();
        //Reviso si la empresa ya tiene un logo, de ser así lo elimino y monto el nuevo


        return "Logo";
    }

    public function image()
    {
        if (request()->image) {
        }
        return "Image";
    }
}
