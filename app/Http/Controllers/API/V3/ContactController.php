<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\ContactRequest;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Date;

class ContactController extends Controller
{
    public function index(ContactRequest $request)
    {

        // Validar los campos del formulario

        $lastSubmissionTime = Session::get('last_submission_time');
        $currentTime = Date::now();

        if ($lastSubmissionTime && $currentTime->diffInSeconds($lastSubmissionTime) < 300) {
            return back()->with(
                [
                    'message' => 'No puedes enviar mensajes tan rápido. Por favor, espera un momento.',
                    'color' => 'error'
                ]
            );
        }

        Session::put('last_submission_time', $currentTime);

        try {
            $parametros['destinatario'] = 'carneiroluis2@gmail.com';
            $parametros['type'] = 'Contacto';
            $parametros['name'] = request()->name;
            $parametros['email'] = request()->email;
            $parametros['phone'] = request()->phone;
            $parametros['mensaje'] = request()->message;

            dispatch(new SendEmailJob($parametros));

            return redirect()->route('contacto')->with([
                'message' => 'Ya recibimos su correo, en breve un asistente de ventas se pondrá en contacto con usted',
                'color' => 'success'
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('contacto')->with([
                'message' => 'Ocurrió un error inesperado, por favor intenta de nuevo',
                'color' => 'danger'
            ]);
        }
    }
}


// EJEMPLO DE TOASTR

// return redirect()->route('your route name')->with('message', 'Data added Successfully');
// return redirect()->route('your route name')->with('error', 'Data Deleted');
// return redirect()->route('your route name')->with('Warning', 'Are you sure you want to delete? ');
// return redirect()->route('your route name')->with('info', 'This is xyz information');
