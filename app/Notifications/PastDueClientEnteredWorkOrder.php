<?php

namespace App\Notifications;

use App\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PastDueClientEnteredWorkOrder extends Notification implements ShouldQueue
{
    public $work_order_id;

    public $notification;

    public $user;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($work_order_id, $notification, $user)
    {
        $this->work_order_id = $work_order_id;
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
        //return ['broadcast','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $wo = WorkOrder::findOrFail($this->work_order_id);

        return (new MailMessage)
                    ->line('Work Order #'.$wo->number.' was created by a client with a past due balance.')
                    ->action('View Work Order', route('workorders.edit', $this->work_order_id))
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
        $wo = WorkOrder::findOrFail($this->work_order_id);

        $url = route('workorders.edit', $wo->id);

        return [
            'note_id' => $this->work_order_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $wo = WorkOrder::findOrFail($this->work_order_id);

        $url = route('workorders.edit', $wo->id);

        return new BroadcastMessage([
            'note_id' => $this->work_order_id,
            'message' => $this->notification,
            'user' => $this->user,
            'url_admin' => $url,
        ]);
    }
}
