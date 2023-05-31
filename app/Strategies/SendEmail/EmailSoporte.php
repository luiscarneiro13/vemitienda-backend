<?php

namespace App\Strategies\SendEmail;

use App\Mail\EmailSoporte as MailSoporte;
use App\Strategies\SendEmailInterface;
use Exception;
use Illuminate\Support\Facades\Mail;

class EmailSoporte implements SendEmailInterface
{
    public function sendEmail($data)
    {
        try {
            Mail::to($data['destinatario'])->send(new MailSoporte($data));
        } catch (Exception $th) {
            info('No se pudo enviar correo a ' . $data['destinatario']);
            return null;
        }
    }
}
