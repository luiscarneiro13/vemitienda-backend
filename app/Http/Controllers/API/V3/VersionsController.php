<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Models\Version;
use Illuminate\Http\Request;

class VersionsController extends Controller
{
    public function index()
    {
        $versionApp = request()->version;
        $version = Version::orderBy('id', 'desc')->first();
        if ($versionApp != $version->version) {
            return response()->json(["actualizar" => true, "versionApp"=>$versionApp,"version"=>$version->version]);
        } else {
            return response()->json(["actualizar" => false, "versionApp"=>$versionApp,"version"=>$version->version]);
        }
    }
}
