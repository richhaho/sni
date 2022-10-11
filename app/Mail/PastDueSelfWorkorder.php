<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\WorkOrder;

class PastDueSelfWorkorder extends Mailable
{
    use Queueable, SerializesModels;
    public $work_order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($work_order)
    {
        $this->work_order = $work_order;
        $this->subject('Workorder Due Soon');

        $from = \App\FromEmails::where('class', 'PastDueSelfWorkorder')->first();
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
        return $this->markdown('emails.pastdue');
    }
}
