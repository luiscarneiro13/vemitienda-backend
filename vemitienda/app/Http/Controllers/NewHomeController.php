<?php

namespace App\Http\Controllers;

use App\Helpers\Visits;
use App\Models\Faq;
use App\Models\Testimony;
use App\Models\Visit;
use Illuminate\Http\Request;

class NewHomeController extends Controller
{

    public $visits;

    public function __construct()
    {
        $this->visits = new Visits();
    }

    public function index(Request $request)
    {
        $this->visits->index($request);
        $data['testimonies'] = Testimony::orderBy('id', 'desc')->get();
        $data['faqs'] = Faq::orderBy('id', 'desc')->get();
        $data['visits'] = Visit::count();
        return view('newWelcome', $data);
    }
}
