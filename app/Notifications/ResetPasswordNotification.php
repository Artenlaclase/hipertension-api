<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url("/reset-password?token={$this->token}&email={$notifiable->email}");

        return (new MailMessage)
            ->subject('Recuperar contraseña — HTApp')
            ->greeting("¡Hola {$notifiable->name}!")
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace expira en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este correo.')
            ->salutation('Equipo HTApp');
    }
}
