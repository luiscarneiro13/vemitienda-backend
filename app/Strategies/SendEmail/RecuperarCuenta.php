<?php

namespace App\Strategies\SendEmail;

use App\Mail\RecuperarCuenta as MailRecuperarCuenta;
use App\Strategies\SendEmailInterface;
use Exception;
use Illuminate\Support\Facades\Mail;

class RecuperarCuenta implements SendEmailInterface
{
    public function sendEmail($data)
    {
        try {
            Mail::to($data['destinatario'])->send(new MailRecuperarCuenta($data));
        } catch (Exception $th) {
            return null;
        }
    }
}
