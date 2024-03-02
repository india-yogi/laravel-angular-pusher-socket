<?php

namespace App\Jobs;

// This job is being executed in Contro Panel Service, it is here just for signature
// This class's Signature should be exactly same as the Job class in Control Panel Service, otherwise 
// it will not get picked up by the Control Panel Service job queue worker
class LoggingJob extends Job
{
    protected $log_service_url, $headers, $data, $method, $data_type;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($log_service_url, $headers, $debug, $data, $method, $data_type)
    
    {
        $this->log_service_url  = $log_service_url;
        $this->headers          = $headers;
        $this->data             = $data;
        $this->method           = $method;
        $this->data_type        = $data_type;  
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Job is being executed in Contro Panel Service, it is here just for signature
    }
}
