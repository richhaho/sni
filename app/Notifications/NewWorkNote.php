<?php

namespace App\Notifications;
use App\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Auth;

class NewWorkNote extends Notification implements ShouldQueue
{
    public $note_id;
    public $notification;
    public $user;

    public $job_number;
    public $job_name;
    public $work_number;
    public $note_text;
    public $to_researcher;
    use Queueable;
   
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($note_id,$notification,$user,$job_number,$job_name,$work_number,$note_text, $to_researcher = false)
    {
        $this->note_id = $note_id;
        $this->notification = $notification;
        $this->user = $user;

        $this->job_number = $job_number;
        $this->job_name = $job_name;
        $this->work_number = $work_number;
        $this->note_text = $note_text;
        $this->to_researcher = $to_researcher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast','database','mail'];
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
        $from = \App\FromEmails::where('class', 'NewWorkNote')->first();
        if (isset($from->from_email)) {
            $fromEmail = $from->from_email;
            $fromName = $from->from_name;
        }

        $note = Note::findOrFail($this->note_id);
        $url = route('workorders.edit',$note->noteable->id) . '?#notes';
        if (Auth::user()) {
            if (Auth::user()->hasRole(['admin']) || Auth::user()->hasRole(['researcher'])) {
                if (!$this->to_researcher) {
                    $url = route('client.notices.edit',$note->noteable->id) . '?#notes';
                }
            }
        }

        return (new MailMessage)
                    ->from($fromEmail, $fromName)
                    ->line('A new note has been added to a Notice ')
                    ->line('Job Number: '.$this->job_number)
                    ->line('Job Name: '.$this->job_name)
                    ->line('Work Order Number: '.$this->work_number)
                    ->line('Note: '.$this->note_text)
                    ->action('View Note', $url)
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
        $note = Note::findOrFail($this->note_id);
        
        $url_admin = route('workorders.edit',$note->noteable->id) . '?#notes';
        $url_client = route('client.notices.edit',$note->noteable->id) . '?#notes';
        return [
            'note_id' => $this->note_id,
            'message' =>  $this->notification,
            'user' => $this->user ,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ];
    }
    
    public function toBroadcast($notifiable)
    {
        $note = Note::findOrFail($this->note_id);
        
        $url_admin = route('workorders.edit',$note->noteable->id) . '?#notes';
        $url_client = route('client.notices.edit',$note->noteable->id) . '?#notes';
        return new BroadcastMessage([
            'note_id' => $this->note_id,
            'message' =>  $this->notification,
            'user' => $this->user ,
            'url_admin' => $url_admin,
            'url_client' => $url_client,
        ]);
    }
}
