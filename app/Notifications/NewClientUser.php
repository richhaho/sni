<?php

namespace App\Notifications;

use App\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClientUser extends Notification implements ShouldQueue
{
    public $client_id;

    public $notification;

    public $user;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($client_id, $notification, $user)
    {
        $this->client_id = $client_id;
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
        $client = Client::findOrFail($this->client_id);

        return (new MailMessage)
                ->line('A new Client User "'.$client->company_name.'" has been created. Please Review that user and approve.')
                ->action('View Client Detail', route('clients.edit', $this->client_id))
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
        $client = Client::findOrFail($this->client_id);

        $url = route('clients.edit', $client->id);

        return [
            'note_id' => $this->client_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $client = Client::findOrFail($this->client_id);

        $url = route('clients.edit', $client->id);

        return new BroadcastMessage([
            'note_id' => $this->client_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url,
        ]);
    }
}
