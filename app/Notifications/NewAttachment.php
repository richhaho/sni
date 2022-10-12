<?php

namespace App\Notifications;

use App\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Session;

class NewAttachment extends Notification implements ShouldQueue
{
    public $attachment_id;

    public $notification;

    public $custom_message;

    public $user;

    public $type;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($attachment_id, $notification, $custom_message, $user, $type)
    {
        $this->attachment_id = $attachment_id;
        $this->notification = $notification;
        $this->custom_message = $custom_message;
        $this->user = $user;
        $this->type = $type;
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
        $fromEmail = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
        $from = \App\FromEmails::where('class', 'NewAttachment')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }
        $attachment = Attachment::findOrFail($this->attachment_id);
        if ($this->type == 'job') {
            $url_admin = route('jobs.edit', $attachment->attachable->id).'?#attachments';
            $url_client = route('client.jobs.edit', $attachment->attachable->id).'?#attachments';
        } else {
            $url_admin = route('workorders.edit', $attachment->attachable->id).'?#attachments';
            $url_client = route('client.notices.edit', $attachment->attachable->id).'?#attachments';
        }
        // if ($this->user->hasRole('admin') || $this->user->hasRole('researcher')) {
        //     $xurl = $url_admin;
        // } else {
        //     $xurl = $url_client;
        // }
        $xurl = $url_client;
        if (Session::get('user_role') == 'admin' || Session::get('user_role') == 'researcher') {
            $xurl = $url_admin;
        } else {
            $xurl = $url_client;
        }

        return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->line('A new Attachment has been added to a '.$this->type)
                    ->line($this->custom_message)
                    ->action('View Attachment', $xurl)
                    ->line('Thank you for using Sunshine Notices!')
                    ->attach(storage_path('app/'.$attachment->file_path));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $attachment = Attachment::findOrFail($this->attachment_id);

        if ($this->type == 'job') {
            $url_admin = route('jobs.edit', $attachment->attachable->id).'?#attachments';
            $url_client = route('client.jobs.edit', $attachment->attachable->id).'?#attachments';
        } else {
            $url_admin = route('workorders.edit', $attachment->attachable->id).'?#attachments';
            $url_client = route('client.notices.edit', $attachment->attachable->id).'?#attachments';
        }

        return [
            'attachment_id' => $this->attachment_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $attachment = Attachment::findOrFail($this->attachment_id);

        if ($this->type == 'job') {
            $url_admin = route('jobs.edit', $attachment->attachable->id).'?#attachments';
            $url_client = route('client.jobs.edit', $attachment->attachable->id).'?#attachments';
        } else {
            $url_admin = route('workorders.edit', $attachment->attachable->id).'?#attachments';
            $url_client = route('client.notices.edit', $attachment->attachable->id).'?#attachments';
        }

        return new BroadcastMessage([
            'attachment_id' => $this->attachment_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ]);
    }
}
