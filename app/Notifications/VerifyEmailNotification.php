<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu cuenta — HTApp')
            ->greeting("¡Hola {$notifiable->name}!")
            ->line('Gracias por registrarte en HTApp para el control de tu salud cardiovascular.')
            ->action('Verificar Email', $url)
            ->line('Si no creaste esta cuenta, ignora este correo.')
            ->salutation('Equipo HTApp');
    }

    protected function verificationUrl($notifiable)
    {
        return url("/email/verify/{$notifiable->getKey()}/" . sha1($notifiable->getEmailForVerification()));
    }
}
