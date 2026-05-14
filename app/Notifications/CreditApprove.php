<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class CreditApprove extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param array $receivers
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toTelegram($notifiable)
    {
        $showroom = "";
        if($this->item['showroom_id'] ===1){
            $showroom = '(М1)';
        }else if($this->item['showroom_id'] ===2){
            $showroom = '(Склад Щелковская)';
        }else if($this->item['showroom_id'] ===4){
            $showroom = '(АвтоПремиум)';
        }else if($this->item['showroom_id'] ===5){
            $showroom = '(Авангард Юг)';
        }else if($this->item['showroom_id'] ===7){
            $showroom = '(Форсаж)';
        }else if($this->item['showroom_id'] ===8){
            $showroom = '(АвтоПремьер)';
        }else if($this->item['showroom_id'] ===10){
            $showroom = '(Автодом)';
        }else if($this->item['showroom_id'] ===13){
            $showroom = '(Автоплюс)';
        }else if($this->item['showroom_id'] ===14){
            $showroom = '(Автопорт)';
        }else if($this->item['showroom_id'] ===15){
            $showroom = '(Авангард)';
        }else if($this->item['showroom_id'] ===17){
            $showroom = '(Автополе)';
        }
        return TelegramMessage::create()
            // Optional recipient user id.
            ->to($notifiable->tg_id)
            // Markdown supported.
            ->content(
                "Клиент по имени *" . $this->item['client_name'] . "* одобрен по кредиту. *$showroom*");

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
