<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoteConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $poll;
    public $voter;
    public $confirmationUrl;

    public function __construct($poll, $voter, $confirmationUrl)
    {
        $this->poll = $poll;
        $this->voter = $voter;
        $this->confirmationUrl = $confirmationUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirm Your Vote for ' . $this->poll->title)
            ->greeting('Hello ' . ($this->voter->name ?: 'Voter') . ',')
            ->line('Thank you for voting in the poll: ' . $this->poll->title)
            ->line('To confirm your vote, please click the button below:')
            ->action('Confirm Vote', $this->confirmationUrl)
            ->line('If you did not submit this vote, you can ignore this email.');
    }
}
