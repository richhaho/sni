<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRelease extends Notification implements ShouldQueue
{
    public $wo_id;

    public $notification;

    public $user;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($wo_id, $notification, $user)
    {
        $this->wo_id = $wo_id;
        $this->notification = $notification;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new Release Notice has been created for this job ')
                    ->action('View Notice', url(route('jobs.edit', $this->wo_id).'?#attachments'))
                    ->line('Thank you for using Sunshine Notices!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $url_admin = route('jobs.edit', $this->wo_id).'?#attachments';
        $url_client = '';

        return [
            'note_id' => $this->wo_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $url_admin = route('jobs.edit', $this->wo_id).'?#attachments';
        $url_client = '';

        return new BroadcastMessage([
            'note_id' => $this->wo_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ]);
    }
}
