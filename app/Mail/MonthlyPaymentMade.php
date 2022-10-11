<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonthlyPaymentMade extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $payment;
    public $client;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment, $client, $type = "mornitoring users")
    {
        $this->payment =$payment;
        $this->client = $client;
        $this->type = $type;

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
        return $this->markdown('emails.monthly_payment');
    }
}
