<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentMade extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $payment_amount;
    public $invoices;
    public $client;
    public $transaction_date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment_amount,$invoices,$client,$transaction_date)
    {
        $this->payment_amount =$payment_amount;
        $this->invoices = $invoices;
        $this->client = $client;
        $this->transaction_date = $transaction_date;

        $from = \App\FromEmails::where('class', 'PaymentMade')->first();
        if (isset($from->from_email)) {
            $this->from[] = [
                'address' => $from->from_email,
                'name' => $from->from_name
            ];
        }

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.payment');
    }
}
