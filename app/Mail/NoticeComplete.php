<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\WorkOrder;

class NoticeComplete extends Mailable
{
    use Queueable, SerializesModels;
    public $work_order;
    public $invoices;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($work_order_id,$invoices)
    {
        $this->work_order = WorkOrder::findOrFail($work_order_id);
        $this->invoices = $invoices;

        $from = \App\FromEmails::where('class', 'NoticeComplete')->first();
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
        return $this->markdown('emails.completed');
    }
}
