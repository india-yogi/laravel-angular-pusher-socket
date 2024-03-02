<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\HelperTrait;

class Localization
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
        app('translator')->setLocale($request->header('Locale'));
        $request->merge(['client_id'=>$request->header('Clientid')]); 

        return $next($request);
    }
}
