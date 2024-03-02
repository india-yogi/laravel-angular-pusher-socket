<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Events\FetchOrders;
use App\Traits\HelperTrait;

/**
 * FetchOrdersListners will listen to FetchOrders event originated from anywhere (mainly from APIGateway/Frontend)
 * It will fetch orders from DB and generate another event to notify originator
 * @author cygnet
 */
class FetchOrdersListener implements ShouldQueue
{
    use HelperTrait, InteractsWithQueue;

    public $queue = 'orders_queue';

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 5;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->job = true;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\FetchOrders $event
     * @return mixed
     */
    public function handle(FetchOrders $event)
    {
        \Log::info("Attempts - ".$this->attempts());
        $co_relation_id = $event->co_relation_id;
        $request = $event->request;

        $db_connection = $request['dbconnection'];
        $db_connection = unserialize($db_connection);
        $orders = [];
        if(!empty($db_connection)){
            $this->setConnectionWithTenantDB(
                $db_connection['username'],
                $db_connection['password'],
                $db_connection['database_name'],
                $db_connection['host'],
                $db_connection['port']
            );

            $req = new Request($request);
            $orders = app('App\Http\Controllers\v1\OrderController')->index($req);
        }


        \Log::debug(json_encode($orders));
        \Log::debug("in fetch orders listenerrr...xxxyyyy");
        \Log::debug($request);
        \Log::debug($co_relation_id);
    }
}
