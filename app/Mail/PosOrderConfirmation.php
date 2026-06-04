<?php

namespace App\Mail;

use App\Models\PosOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PosOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public PosOrder $order;

    public function __construct(PosOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Confirmation — ' . $this->order->order_number)
                    ->view('emails.pos-order-confirmation');
    }
}
