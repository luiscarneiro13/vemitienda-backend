<?php

namespace App\Strategies\SendEmail;

use App\Mail\Contacto as MailContacto;
use App\Strategies\SendEmailInterface;
use Exception;
use Illuminate\Support\Facades\Mail;

class Contacto implements SendEmailInterface
{
    public function sendEmail($data)
    {
        try {
            Mail::to($data['destinatario'])->send(new MailContacto($data));
        } catch (Exception $th) {
            return null;
        }
    }
}
