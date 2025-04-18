<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{
    use Queueable;

    public string $code;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code)
    {
      $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
            ->subject(Lang::get('Запрос на смену пароля'))
            ->line('Если вы не отправляли запрос на смену пароля, то проигнорируйте данное письмо.')
            ->action('Восстановление пароля', url('/'))
            ->line('Нажмите кнопку ниже, чтобы задать новый пароль')
            ->view('emails.reset-password',  ['code' => $this->code]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
