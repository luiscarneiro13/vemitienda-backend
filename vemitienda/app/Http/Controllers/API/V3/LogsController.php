<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        info(json_encode($request->all()));
    }
}
