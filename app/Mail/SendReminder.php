<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Reminders;


class SendReminder extends Mailable
{
    use Queueable, SerializesModels;
    public $client;
    public $message;
    public $subject;    
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($client,$message,$subject)
    {
       $this->client =$client;
       $this->message =$message;
       $this->subject =$subject;
       $this->subject($subject);

        $from = \App\FromEmails::where('class', 'SendReminder')->first();
        if (isset($from->from_email)) {
            $this->from[] = [
                'address' => $from->from_email,
                'name' => $from->from_name
            ];
        } else {
            $this->from[] = [
                'address' => env('REMINDER_FROM'),
                'name' => env('REMINDER_FROM_NAME')
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
        return $this->markdown('emails.reminder');
    }

    
}
