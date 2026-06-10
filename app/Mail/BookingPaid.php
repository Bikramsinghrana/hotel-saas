<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingPaid extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $invoice;

    public function __construct($order, $invoice = null)
    {
        $this->order = $order;
        $this->invoice = $invoice;
    }

    public function build()
    {
        $mail = $this->subject('Booking Confirmed — ' . ($this->order->order_number ?? 'Order'))
            ->view('emails.booking_paid')
            ->with(['order' => $this->order, 'invoice' => $this->invoice]);

        if ($this->invoice && !empty($this->invoice->pdf_path) && \Storage::disk('public')->exists($this->invoice->pdf_path)) {
            $mail->attach(\Storage::disk('public')->path($this->invoice->pdf_path));
        }

        return $mail;
    }
}
