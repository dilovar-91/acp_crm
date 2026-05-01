<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClearNotify implements  ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    //public $info;
    public $showroom_id;


    public function __construct($showroom_id)
    {
        //$this->info = $info;
        $this->showroom_id = $showroom_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('clear_'.$this->showroom_id);
    }
}
