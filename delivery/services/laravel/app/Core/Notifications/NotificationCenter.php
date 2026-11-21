<?php

namespace App\Core\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Punto único de envío de notificaciones. Ningún módulo debe llamar a
 * Notification::send() directamente — siempre a través de este servicio
 * (helper `notification()`), para poder centralizar logging, throttling
 * o reglas de envío a futuro sin tocar cada call site.
 *
 * Canales soportados hoy: database, mail (nativos de Laravel). La
 * arquitectura no cambia para añadir sms/push/whatsapp/telegram/slack:
 * son canales estándar de Laravel (o paquetes de
 * laravel-notification-channels/*) que se añaden en el `via()` de cada
 * Notification — ver App\Core\Notifications\Contracts\NotificationChannelInterface
 * para canales completamente custom.
 */
class NotificationCenter
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        NotificationFacade::send($notifiable, $notification);
    }

    public function sendNow(mixed $notifiable, Notification $notification): void
    {
        NotificationFacade::sendNow($notifiable, $notification);
    }

    /**
     * @param  iterable<mixed>  $notifiables
     */
    public function sendToMany(iterable $notifiables, Notification $notification): void
    {
        NotificationFacade::send($notifiables, $notification);
    }
}
