<?php

namespace App\Listeners;

use App\Events\ProductUpdated;
use App\Models\User;
use App\Notifications\ProductChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductEventListener
{

    public function onUpdated($event)
    {
        $user = User::query()->where("email","user@user.com")->first();

        if($user){
            $user->notify(new ProductChange($event->product));
        }


    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            ProductUpdated::class,
            'App\Listeners\ProductEventListener@onUpdated'
        );

    }
}
