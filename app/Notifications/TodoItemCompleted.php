<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TodoItemCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $work_order;

    protected $user;

    protected $todo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($work_order, $user, $todo)
    {
        $this->work_order = $work_order;
        $this->user = $user;
        $this->todo = $todo;
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
        $from = \App\FromEmails::where('class', 'TodoItemCompleted')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }

        return (new MailMessage)
                ->from($fromEmail, $fromName)
                ->line($this->user->full_name.' has completed TODO item: '.$this->todo->name.' on work order: #'.$this->work_order->number)
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $url_admin = route('workorders.todo.edit', ['work_id' => $this->work_order->id, 'id' => $this->todo->id]);
        $url_client = route('client.notices.todo.edit', ['work_id' => $this->work_order->id, 'id' => $this->todo->id]);
        $ndata = [
            'note' => 'Todo '.$this->todo->name.' completed on Workorder #'.$this->work_order->number,
            'entered_at' => $this->todo->completed_at,
        ];

        return [
            'note_id' => $this->work_order->id,
            'message' => $ndata,
            'user' => $this->user,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $url_admin = route('workorders.todo.edit', ['work_id' => $this->work_order->id, 'id' => $this->todo->id]);
        $url_client = route('client.notices.todo.edit', ['work_id' => $this->work_order->id, 'id' => $this->todo->id]);
        $ndata = [
            'note' => 'Todo '.$this->todo->name.' completed on Workorder #'.$this->work_order->number,
            'entered_at' => $this->todo->completed_at,
        ];

        return new BroadcastMessage([
            'note_id' => $this->work_order->id,
            'message' => $ndata,
            'user' => $this->user,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ]);
    }
}
