<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Events\OrdersFetched;
use App\Traits\HelperTrait;

/**
 * FetchOrdersListners will listen to FetchOrders event originated from anywhere (mainly from APIGateway/Frontend)
 * It will fetch orders from DB and generate another event to notify originator
 * @author cygnet
 */
class OrdersFetchedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Event $event
     * @return mixed
     */
    public function handle(Event $event_1, Event $event_2)
    {
//        $this->release();
        \Log::debug("Procurement service OrdersFetchedListner...");
    }
}
