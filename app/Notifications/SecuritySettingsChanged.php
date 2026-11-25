<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells the account that a sign-in method changed, so an owner whose session was misused can react before the
 * attacker's passkey or two-factor setup locks them out.
 */
class SecuritySettingsChanged extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly string $description) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return list<string>
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
            ->subject(__('Your account security settings changed'))
            ->line($this->description)
            ->line(__('If you did not make this change, change your password and review your passkeys and two-factor settings.'))
            ->action(__('Review security settings'), route('security.edit'));
    }
}
