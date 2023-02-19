<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\ContactRequest;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(ContactRequest $request)
    {
        try {
            $parametros['name'] = request()->name;
            $parametros['destinatario'] = 'vemitienda@gmail.com';
            $parametros['type'] = 'Contacto';
            $parametros['data'] = request()->all();

            dispatch(new SendEmailJob($parametros));

            return redirect()->route('contacto')->with([
                'message'=> 'Ya recibimos su correo, en breve un asistente de ventas se pondrá en contacto con usted',
                'color'=>'success'
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('contacto')->with([
                'message'=> 'Ocurrió un error inesperado, por favor intenta de nuevo',
                'color'=>'danger'
            ]);
        }
    }
}


// EJEMPLO DE TOASTR

// return redirect()->route('your route name')->with('message', 'Data added Successfully');
// return redirect()->route('your route name')->with('error', 'Data Deleted');
// return redirect()->route('your route name')->with('Warning', 'Are you sure you want to delete? ');
// return redirect()->route('your route name')->with('info', 'This is xyz information');
