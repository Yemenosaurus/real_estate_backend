<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscriber;

class NewsletterConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $confirmationUrl = url("/api/newsletter/confirm/{$this->subscriber->confirmation_token}");
        $unsubscribeUrl = url("/api/newsletter/unsubscribe/" . encrypt($this->subscriber->email));

        return (new MailMessage)
            ->subject('Confirmez votre inscription à la newsletter')
            ->view('emails.newsletter-confirmation', [
                'subscriber' => $this->subscriber,
                'confirmationUrl' => $confirmationUrl,
                'unsubscribeUrl' => $unsubscribeUrl
            ]);
    }
} 