<?php

namespace App\Helpers;

use App\Strategies\SendEmail\ActivarCuenta;
// use App\Strategies\SendEmail\RecuperarCuenta;

class SendMail
{
    const STRATEGY = [
        'activarcuenta' => ActivarCuenta::class,
    ];

    public function send($state, $data)
    {
        $strategyClass = SELF::STRATEGY[$state];

        return (new $strategyClass)->sendEmail($data);
    }
}
