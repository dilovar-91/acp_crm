<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MangoIncome implements  ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $response;
    public $showroom_id;

    /**
     * Create a new event instance.
     *
     * @param $response
     */
    public function __construct($response, $showroom_id)
    {
        $this->response = $response;
        $this->showroom_id = $showroom_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */

    public function broadcastOn()
    {
        //return new php artisan tinker('calling');
        //Log::emergency($this->response);
        //Log::emergency('calling_'.$this->showroom_id.'_'.$this->response['work_place']);
        return new Channel('calling_'.$this->showroom_id);
    }
}
