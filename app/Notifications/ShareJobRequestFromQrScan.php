<?php

namespace App\Notifications;
use App\Job;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShareJobRequestFromQrScan extends Notification
{
    use Queueable;
    protected $job;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Job $job, User $user)
    {
       $this->job =$job;
       $this->user =$user;
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
        $from = \App\FromEmails::where('class', 'ShareJobRequestFromQrScan')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }
        return (new MailMessage)
                ->from($fromEmail, $fromName)
                ->subject("Share the job to monitoring user")
                ->line($this->user->full_name ." has requested you share your job summary report with them.")
                ->line("Job name: ". $this->job->name)
                ->line("If you accept their request please click below button.")
                ->action('Accept', url('/client/jobs/'. $this->job->id.'/share_to/'. $this->user->id))
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
