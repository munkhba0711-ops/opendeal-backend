<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $resetUrl;

    public function __construct($url)
    {
        $this->resetUrl = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Нууц үг сэргээх хүсэлт')
            ->line('Та нууц үгээ сэргээх хүсэлт гаргасан тул доорх товч дээр дарна уу.')
            ->action('Нууц үг сэргээх', $this->resetUrl)
            ->line('Хэрэв та хүсэлт гаргаагүй бол энэ имэйлийг үл тоомсорлоорой.');
    }
}
