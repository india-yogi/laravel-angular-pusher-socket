<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;

class EmailJob extends Job implements ShouldQueue
{
    protected $client_id, $action, $data, $to, $reciepients;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($client_id, $action, $data, $to)
    {
        $this->client_id    = $client_id;
        $this->action       = $action;
        $this->data         = $data;
        $this->to           = $to;
        $this->reciepients  = '';
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // no implementation here, implemented in ControlPanelService (jobs)
    }
}
