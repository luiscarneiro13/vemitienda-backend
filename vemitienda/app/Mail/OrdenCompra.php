<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdenCompra extends Mailable
{
    use Queueable, SerializesModels;

    protected $datos;
    public $vista;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
        $this->subject = 'Orden de compra n° ' . $datos['order']->id;
        $this->vista = 'Mails.Order';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->vista)->with($this->datos);
    }
}
