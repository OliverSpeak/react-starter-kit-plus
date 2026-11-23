<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells the previous address that the account's email changed, so an owner whose session was misused can react.
 */
class EmailAddressChanged extends Notification
{
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
            ->subject(__('Your email address was changed'))
            ->line(__('The email address for your :app account was changed. Account emails, including password reset links, now go to the new address.', [
                'app' => config('app.name'),
            ]))
            ->line(__('If you did not make this change, someone else may have access to your account.'));
    }
}
