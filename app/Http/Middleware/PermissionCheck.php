<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$permission)
    {
        if (!Auth::check()){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>"未登录！"
            ]);
        }
        $user = Auth::user();
        if ($user->can('logo')){
            return $next($request);
        }
        return response()->json([
            'return_code'=>'FAIL',
            'return_msg'=>"无权操作！"
        ]);

    }
}
