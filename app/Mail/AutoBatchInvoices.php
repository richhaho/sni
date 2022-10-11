<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoBatchInvoices extends Mailable
{
    use Queueable, SerializesModels;
     
    public $invoicebatch;
    public $client_name;
    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf,$invoicebatch,$client_name,$company_name)
    {
        $this->invoicebatch = $invoicebatch;
        $this->client_name=$client_name;
        $this->pdf=$pdf;
        $this->subject($company_name.' - AutoBatch Invoices - Sunshine Notices');

        $from = \App\FromEmails::where('class', 'AutoBatchInvoices')->first();
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
        return $this->markdown('emails.autobatchinvoices')->attach(storage_path($this->pdf),[
                'as' => 'autobatch.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
