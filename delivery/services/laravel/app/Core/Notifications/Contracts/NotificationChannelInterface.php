<?php

namespace App\Core\Notifications\Contracts;

use Illuminate\Notifications\Notification;

/**
 * Contrato de referencia para canales de notificación custom que no son
 * drivers nativos de Laravel (SMS, Push, WhatsApp, Telegram, Slack...).
 * Un canal Laravel estándar ya es una clase con `send($notifiable,
 * Notification $notification)`; esta interfaz solo formaliza esa forma
 * dentro del Core para que los módulos la sigan de manera consistente.
 * Se registra como canal igual que cualquier otro: añadiéndolo al array
 * que devuelve `via()` en la clase Notification.
 */
interface NotificationChannelInterface
{
    public function send(mixed $notifiable, Notification $notification): void;
}
