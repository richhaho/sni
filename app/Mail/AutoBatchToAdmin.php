<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoBatchToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $batches;

    public $period;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($batches, $period)
    {
        $this->batches = $batches;
        $this->period = $period;

        $this->subject('Invoice Batches Generated at '.$period);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.autobatchtoadmin');
    }
}
