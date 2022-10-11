<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendToPartiesOnNtoAndAnto extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $job;
    public $over;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job,$over)
    {
        $this->job = $job;
        $this->over = $over;
        $this->subject($this->job->name." - ".$this->over." DAYS SINCE LAST DAY ON JOB");

        $from = \App\FromEmails::where('class', 'SendToPartiesOnNtoAndAnto')->first();
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
        return $this->markdown('emails.lastdayover');
    }
}
