<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Build the reset URL including the email as a query param
        $url = url(route('password.reset', $this->token, false) . '?email=' . urlencode($notifiable->getEmailForPasswordReset()));
        // Use a custom html view so we can include the app logo and richer layout
        return (new MailMessage)
                    ->subject(config('app.name') . ' — Password Reset Request')
                    ->view('emails.passwords.reset', [
                        'url' => $url,
                        'notifiable' => $notifiable,
                        'expire' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                    ]);
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
            'token' => $this->token,
        ];
    }
}
