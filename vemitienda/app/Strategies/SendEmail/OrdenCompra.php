<?php

namespace App\Strategies\SendEmail;

use App\Mail\OrdenCompra as MailOrdenCompra;
use App\Strategies\SendEmailInterface;
use Exception;
use Illuminate\Support\Facades\Mail;

class OrdenCompra implements SendEmailInterface
{
    public function sendEmail($data)
    {
        try {
            Mail::to($data['destinatario'])->send(new MailOrdenCompra($data));
        } catch (Exception $th) {
            return null;
        }
    }
}
