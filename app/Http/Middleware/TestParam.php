<?php

namespace App\Http\Middleware;

use Closure;

class TestParam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$param)
    {
        if ($param=='test'){
            return $next($request);
        }else{
            return response()->json([
                'return_code'=>'no'
            ]);
        }
    }
}
