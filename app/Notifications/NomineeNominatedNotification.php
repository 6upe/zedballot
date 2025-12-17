<?php

namespace App\Notifications;

use App\Models\Poll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NomineeNominatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Poll $poll,
        public string $voteUrl
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been nominated in: ' . $this->poll->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have been nominated as a candidate in the poll: **' . $this->poll->name . '**')
            ->line($this->poll->description)
            ->action('View Poll & Vote', $this->voteUrl)
            ->line('Thank you for participating!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'poll_id' => $this->poll->id,
            'poll_name' => $this->poll->name,
            'vote_url' => $this->voteUrl,
        ];
    }
}
