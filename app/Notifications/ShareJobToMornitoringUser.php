<?php

namespace App\Notifications;

use App\Job;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShareJobToMornitoringUser extends Notification
{
    use Queueable;

    protected $job;

    protected $user;

    protected $is_success;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Job $job, User $user, $is_success)
    {
        $this->job = $job;
        $this->user = $user;
        $this->is_success = $is_success;
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
        $from = \App\FromEmails::where('class', 'ShareJobToMornitoringUser')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }
        if ($this->is_success) {
            return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->subject('A Job was shared to you')
                    ->line($this->user->full_name.' shared a job to you.')
                    ->line('Job name: '.$this->job->name)
                    ->line('Thank you for using Sunshine Notices!');
        } else {
            return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->subject('Sub/vendor tried to share a job to you')
                    ->line($this->user->full_name.' tried to share a job to you but it was failed because you are not a mornitoring user. If you want to get shared the job, please contact Sunshine support.')
                    ->line('Job name: '.$this->job->name)
                    ->line('Thank you for using Sunshine Notices!');
        }
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
