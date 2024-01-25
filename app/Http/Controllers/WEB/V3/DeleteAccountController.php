<?php

namespace App\Http\Controllers\WEB\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteAccountController extends Controller
{
    public function deleteAccount(){
        return view('deleteAccount');
    }
}
