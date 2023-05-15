<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Testimony;
use Illuminate\Http\Request;

class NewHomeController extends Controller
{
    public function index()
    {
        $data['testimonies'] = Testimony::orderBy('id', 'desc')->get();
        $data['faqs'] = Faq::orderBy('id', 'desc')->get();
        return view('newWelcome', $data);
    }
}
