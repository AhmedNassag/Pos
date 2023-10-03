<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StockAlert extends Notification
{
    use Queueable;
    
    public function __construct()
    {
    }

    
    
    public function via($notifiable)
    {
        return [/*'mail',*/ 'database'];
    }

    
    
    /*
    public function toMail($notifiable)
    {
        $url = 'http://127.0.0.1:8000/category/'.$this->id;
        return (new MailMessage)
            ->subject('Coding System')
            ->line('New Notification.')
            ->action('Show', $url)
            ->line('Thank you for using our application!');
    }
    */

    
    
    public function toDatabase($notifiable)
    {
        return [
            'route' => 'stockAlertSNotification',
            'title' => 'هناك تنبيه لكمية مخزون بواسطة',
            'user'  => Auth::user()->name,
        ];
    }
}
