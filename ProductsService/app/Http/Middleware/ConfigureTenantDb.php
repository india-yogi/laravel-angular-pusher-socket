<?php

namespace App\Http\Middleware;

use Closure;
use App\TenantDbConfig;
use App\Traits\HelperTrait;

class ConfigureTenantDb
{
    use HelperTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->merge(['client_id'=>$request->header('Clientid')]);
        
        $dbconnection = $request['dbconnection'];
        $dbconnection = unserialize($dbconnection);
        if(isset($dbconnection) && !empty($dbconnection)){
            $this->setConnectionWithTenantDB($dbconnection['username'],$dbconnection['password'],$dbconnection['database_name'],$dbconnection['host'],$dbconnection['port']);  
        }
        return $next($request);
    }
}
