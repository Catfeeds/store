<?php

namespace App\Http\Middleware;

use Closure;

class cross
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
//        $response->header('Access-Control-Allow-Origin', 'http://192.168.3.44:8080');
//        $response->header('Access-Control-Allow-Origin', 'http://119.23.255.177:8092');
        $response->header('Access-Control-Allow-Origin', 'http://admin.ankekan.com');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET,POST,PATCH,PUT,OPTIONS,DELETE');
        $response->header('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}
