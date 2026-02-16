<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoteConfirmationNotification extends Notification
{
    use Queueable;

    public $poll;
    public $voter;
    public $eligibleVoter;
    public $confirmationUrl;

     public function __construct($poll, $voter, $eligibleVoter, $confirmationUrl)
    {
        $this->poll = $poll;
        $this->voter = $voter;
        $this->eligibleVoter = $eligibleVoter;
        $this->confirmationUrl = $confirmationUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vote Confirmation ' . $this->poll->name)
            ->view('emails.vote_confirmation', [
                'poll' => $this->poll,
                'voter' => $this->voter,
                'eligibleVoter' => $this->eligibleVoter,
                'confirmationUrl' => $this->confirmationUrl,
            ]);
    }
}
