<?php

namespace App\Http\Controllers;

use App\Helpers\Images;
use Illuminate\Http\Request;

class PruebasController extends Controller
{
    public function index()
    {
        $images = new Images();
        return $images->getFiles();
    }
}
