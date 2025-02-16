<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        $frontendUrl = config('app.url'); // Ensure this is set in .env

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', "{$frontendUrl}/reset-password?token={$this->token}&email={$notifiable->email}")
            ->line('If you did not request a password reset, no further action is required.');
    }
}
