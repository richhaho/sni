<?php

namespace App\Notifications;
use App\ContractTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContractTrackerNotConverted extends Notification
{
    use Queueable;
    protected $contract_tracker;
    protected $over;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ContractTracker $contract_tracker, $over)
    {
       $this->contract_tracker =$contract_tracker;
       $this->over =$over;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $fromEmail = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
        $from = \App\FromEmails::where('class', 'ContractTrackerNotConverted')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }
        return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->subject($this->contract_tracker->name." - ".$this->over." DAYS SINCE CREATED")
                    ->line("Contract tracker name: ". $this->contract_tracker->name)
                    ->line("This is a reminder that if you have contract tracker not converted to job or notice yet. Based on the created day entered, it's over ".$this->over." days from created date until start date + 35 days. Click the button below to review the contract tracker.")
                    ->action('Click Here', url('/client/contract_trackers'))
                    ->line('Thank you for using Sunshine Notices!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
