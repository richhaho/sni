<?php

namespace App\Notifications;

use App\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShareJobSelectedOnWorkorderCreation extends Notification
{
    use Queueable;

    protected $job;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
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
        $from = \App\FromEmails::where('class', 'ShareJobSelectedOnWorkorderCreation')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }

        return (new MailMessage)
                ->from($fromEmail, $fromName)
                ->subject('New Work Order on Shared Job')
                ->line('The job owner has created a new work order on the below job.')
                ->line('Job name: '.$this->job->name)
                ->line('You can check the job summary by clicking below button.')
                ->action('View Job Summary', url('/client/jobs_shared/'.$this->job->id.'/summary/'))
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
