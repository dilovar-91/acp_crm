<?php

namespace App\Providers;

use App\Events\ClientProcessed;
use App\Events\CreditCreated;
use App\Events\MangoIncome;
use App\Events\OrderCreated;
use App\Events\OrderPvCreated;
use App\Events\OrderProcessed;
use App\Listeners\MangoIncomeEventListener;
use App\Listeners\OrderAddedPvEventListener;
use App\Listeners\OrderEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ArrivalCreated;
use App\Events\CarAdded;
use App\Events\NotificationSend;
use App\Listeners\OrderAddedEventListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            ArrivalCreated::class,
            OrderCreated::class,
            CreditCreated::class,
            NotificationSend::class,
            CarAdded::class,
            ClientProcessed::class,
        ],
        OrderCreated::class => [
            OrderAddedEventListener::class,
        ],
        OrderPvCreated::class => [
            OrderAddedPvEventListener::class,
        ],
        MangoIncome::class => [
            MangoIncomeEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}
