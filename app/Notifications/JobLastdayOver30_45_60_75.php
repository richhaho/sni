<?php

namespace App\Notifications;

use App\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobLastdayOver30_45_60_75 extends Notification
{
    use Queueable;

    protected $job;

    protected $over;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Job $job, $over)
    {
        $this->job = $job;
        $this->over = $over;
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
        $from = \App\FromEmails::where('class', 'JobLastdayOver30_45_60_75')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }

        $left = strval(90 - intval($this->over));

        return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->subject($this->job->name.' - '.$this->over.' DAYS SINCE LAST DAY ON JOB')
                    ->line('Job name: '.$this->job->name)
                    ->line('This is a reminder that if you are currently carrying an outstanding balance on this job. Based on the last day entered, you have '.$left.' days left to file your Claim of Lien and/or Notice of Nonpayment (if bonded). Statutory requirements of filing a timely notice to owner must have been met (if required).  If you have been paid in full click the button below.')
                    ->action('Click Here to close the job', url('/client/jobs/'.$this->job->id.'/closelink'))
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
