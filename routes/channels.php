<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('cars', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('arrivals', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('orders_{id}', function ($order, $id) {
    return (int) $order->showroom_id === (int) $id;
});
Broadcast::channel('calling_{id}_{wordkplace}', function ($order, $id) {
    return (int) $order->showroom_id === (int) $id;
});

Broadcast::channel('events', true);
Broadcast::channel('calling', true);
Broadcast::channel('clear_{id}_{wordkplace}', true);

