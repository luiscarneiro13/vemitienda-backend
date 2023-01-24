<?php

namespace App\Strategies\SendEmail;

use App\Mail\ActivarCuenta as MailActivarCuenta;
use App\Strategies\SendEmailInterface;
use Exception;
use Illuminate\Support\Facades\Mail;

class ActivarCuenta implements SendEmailInterface
{
    public function sendEmail($data)
    {
        try {
            info($data);
            Mail::to($data['destinatario'])->send(new MailActivarCuenta($data));
        } catch (Exception $th) {
            return null;
        }
    }
}
