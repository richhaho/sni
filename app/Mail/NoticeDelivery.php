<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class NoticeDelivery extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $recipient;
    public $file_path;
    public $client_company_name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($recipient,$file_path,$client_company_name)
    {
        $this->recipient = $recipient;
        $this->file_path = $file_path;
        $this->client_company_name = $client_company_name;

        $from = \App\FromEmails::where('class', 'NoticeDelivery')->first();
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
        return $this->markdown('emails.deliver')->subject(strtoupper(str_replace('-',' ',$this->recipient->work_order->type)))
            ->attach(storage_path('app/'.$this->file_path), [
                'as' => 'notice.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
