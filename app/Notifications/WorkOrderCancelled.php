<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\WorkOrder;
use App\User;

class WorkOrderCancelled extends Notification implements ShouldQueue
{
    use Queueable;
    protected $work_order;
    protected $user;
    protected $role;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(WorkOrder $work_order, User $user,$role)
    {
       $this->work_order = $work_order;
       $this->user = $user;
       $this->role=$role;
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
        $from = \App\FromEmails::where('class', 'WorkOrderCancelled')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }

        if ($this->role=='admin'){
            return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->line($this ->user->full_name .' has cancelled work order number: ' . $this->work_order->number)
                    ->action('Confirm Cancellation', route('workorders.edit',$this->work_order->id))
                    ->line('Thank you for using Sunshine Notices!');
        }else{
            return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->line($this ->user->full_name .' has cancelled work order number: ' . $this->work_order->number)
                    ->action('View Cancelled Work Order', route('client.notices.edit',$this->work_order->id))
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
