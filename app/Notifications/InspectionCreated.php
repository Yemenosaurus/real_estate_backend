<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PropertyInspection;

class InspectionCreated extends Notification
{
    use Queueable;

    protected $propertyInspection;

    /**
     * Create a new notification instance.
     *
     * @param  PropertyInspection  $propertyInspection
     * @return void
     */
    public function __construct(PropertyInspection $propertyInspection)
    {
        $this->propertyInspection = $propertyInspection;
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
        return (new MailMessage)
                    ->subject('Nouvelle Inspection Créée')
                    ->view('emails.inspection_created', ['propertyInspection' => $this->propertyInspection]);
    }
} 