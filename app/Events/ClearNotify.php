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
    public $showroom_id;
    public $entry_id;

    public function __construct($showroom_id, $entry_id = null)
    {
        $this->showroom_id = (int) $showroom_id;
        $this->entry_id = $entry_id;
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
