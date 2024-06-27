<?php

namespace App\Http\Controllers;

use App\Helpers\Visits;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public $visits;

    public function __construct()
    {
        $this->visits = new Visits();
    }

    public function index(Request $request)
    {
        $this->visits->index($request);
        return view('newContact');
    }

    public function deleteAccount(Request $request)
    {
        return view('deleteAccount');
    }
}
