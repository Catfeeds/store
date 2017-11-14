<?php

namespace App\Http\Middleware;

use Closure;

class tokenCheck
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
        $token = $request->get('token');
        $uid = getUserToken($token);
        if (!$uid){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'请先登录！'
            ]);
        }
        return $next($request);
    }
}
