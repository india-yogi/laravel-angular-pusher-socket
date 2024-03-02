<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class FetchOrders  extends Event
{
    use Dispatchable, SerializesModels;
    public $request;
    public $co_relation_id;

    /**
     * Create a new event instance.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($request, $co_relation_id)
    {
        $this->request = $request;
        $this->co_relation_id = $co_relation_id;
    }
}
