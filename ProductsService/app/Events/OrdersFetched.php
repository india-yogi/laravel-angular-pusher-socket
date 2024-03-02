<?php

namespace App\Events;
class OrdersFetched extends Event
{
    public $orders, $client_id;

    /**
     * Create a new event instance.
     *
     * @param \App\Order $orders
     */
    public function __construct($orders, $client_id)
    {
        $this->orders    = $orders;
        $this->client_id = $client_id;
    }
}
