<?php

namespace App\Http\Middleware;

use App\Models\Member;
use App\Models\MemberLevel;
use App\PublishRecord;
use Closure;

class limitSend
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
        $uid = getUserToken($request->get('token'));
        $member = Member::where('user_id','=',$uid)->first();
        if (empty($member)||$member->end_time<time()){
            $level = MemberLevel::where('level','=',0)->first();
            $count = PublishRecord::where('user_id','=',$uid)->whereDate('created_at','=',date('Y-m-d',time()))->count();
            if ($count>$level->send_daily){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>"发布消息数量已超过每日限额!"
                ]);
            }
            $maxcount = PublishRecord::where('user_id','=',$uid)->count();
            if ($maxcount>$level->send_max){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>"发布消息数量已超过每日限额!"
                ]);
            }
        }else{
            $count = PublishRecord::where('user_id','=',$uid)->whereDate('created_at','=',date('Y-m-d',time()))->count();
            if ($count>$member->send_daily){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>"发布消息数量已超过每日限额!"
                ]);
            }
            $maxcount = PublishRecord::where('user_id','=',$uid)->count();
            if ($maxcount>$member->send_max){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>"发布消息数量已超过每日限额!"
                ]);
            }
        }
        return $next($request);
    }
}
