<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class NotifyConsultationOperator extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($item, $telegramID)
    {
        $this->item = $item;
        $this->telegramID = $telegramID;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
     * @return TelegramMessage
     */
    public function toTelegram($notifiable)
    {
        $showroom = "";
        if($this->item['showroom_id'] ===2){
            $showroom = '(Комфорт)';
        }else if($this->item['showroom_id'] ===3){
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
            // Optional recipient user id.7
            ->to($this->telegramID)
            // Markdown supported.
            ->content(
                "Консультация клиента *" . $this->item['client_name'] . "* по номеру *".$this->item['phone_number']."* от салона $showroom. ".$this->item['car_name'].", Ком.Салон:*". $this->item['comments']."*, Ком.Кц: *". $this->item['sd_comment']."*.");

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
