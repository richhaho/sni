<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscriptionPaymentMade extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $payment;
    public $client;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment, $client)
    {
        $this->payment =$payment;
        $this->client = $client;

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
        return $this->markdown('emails.subscription_payment');
    }
}
