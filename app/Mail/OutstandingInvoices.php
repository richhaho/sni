<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OutstandingInvoices extends Mailable
{
    use Queueable, SerializesModels;
    public $owed_amount;
    public $invoices;
    public $client_name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($owed_amount,$invoices,$client_name,$company_name)
    {
        $this->owed_amount = $owed_amount;
        $this->invoices = $invoices;
        $this->client_name=$client_name;
        $this->subject($company_name.' - Outstanding Invoices - Sunshine Notices');

        $from = \App\FromEmails::where('class', 'OutstandingInvoices')->first();
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
        return $this->markdown('emails.outstandinginvoices');
    }
}
