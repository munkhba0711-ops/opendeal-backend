<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $verificationUrl;

    public function __construct($url)
    {
        $this->verificationUrl = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Имэйл хаяг баталгаажуулах')
            ->greeting('Сайн байна уу!')
            ->line('Та манайд бүртгүүлсэн имэйл хаягаа доорх товч дээр дарж баталгаажуулна уу.')
            ->action('Имэйл баталгаажуулах', $this->verificationUrl)
            ->line('Хэрэв та бүртгүүлээгүй бол энэ имэйлийг үл тоомсорлоно уу.');
    }
}
