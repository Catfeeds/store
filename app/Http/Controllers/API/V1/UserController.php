<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\LoginPost;
use App\Http\Requests\RegisterPost;
use App\Http\Requests\ResetPasswordPost;
use App\Models\Commodity;
use App\Models\MemberLevel;
use App\Models\Score;
use App\Models\Sign;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use League\Flysystem\Config;
use Symfony\Component\CssSelector\Parser\Token;

class UserController extends Controller
{
    //
    public function register(RegisterPost $request)
    {
        $code = $request->get('code');
        $data = getCode($request->get('phone'));
        if (empty($data)||$data['type']!='register'){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'验证码已失效！'
            ],422);
        }
        if ($data['code']!=$code){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'验证码错误！'
            ],422);
        }
        User::create([
            'username' =>  $request->get('username'),
            'phone' => $request->get('phone'),
            'password' => bcrypt($request->get('password')),
        ]);
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);

    }
    public function login(LoginPost $loginPost)
    {
        $username = $loginPost->get('username');
        $password = $loginPost->get('password');
        if (Auth::attempt(['username'=>$username,'password'=>$password],true)){
            $user = Auth::user();
            if ($user->state!=1){
                return response()->json([
                    'return_code'=>"FAIL",
                    'return_msg'=>'账号已被封禁！'
                ],422);
            }
            $key = createNonceStr();
            setUserToken($key,$user->id);
            return response()->json([
                'return_code'=>"SUCCESS",
                'token'=>$key
            ]);
        }else{
            if (Auth::attempt(['phone'=>$username,'password'=>$password],true)){
                $user = Auth::user();
                if ($user->state!=1){
                    return response()->json([
                        'return_code'=>"FAIL",
                        'return_msg'=>'账号已被封禁！'
                    ],422);
                }
                $key = createNonceStr();
                setUserToken($key,$user->id);
                return response()->json([
                    'return_code'=>"SUCCESS",
                    'data'=>[
                        'token'=>$key
                    ]
                ]);
            }else{
                return response()->json([
                    'return_code'=>"FAIL",
                    'return_msg'=>'用户不存在或密码错误！'
                ],422);
            }
        }

    }
    public function resetPassword(ResetPasswordPost $request)
    {

    }
    public function changePassword()
    {

    }
    public function sendCode()
    {

    }
    public function myPublish()
    {
    }
    public function delPublish()
    {

    }
    public function addReport()
    {

    }
    public function addReason()
    {

    }
    public function getToken()
    {
        $token = Input::get('token');
        $uid = getUserToken($token);
        dd($uid);
    }
    public function sign()
    {
        $uid = getUserToken(Input::get('token'));
        $sign = Sign::where('user_id','=',$uid)->whereDate('created_at', date('Y-m-d',time()))->first();
        if (!empty($sign)){
            return response()->json([
                'return_code'=>"ERROR",
                'return_msg'=>'今天已签到!'
            ]);
        }else{
            $sign = new Sign();
        }
        $sign->user_id = $uid;
        $sign->save();
        return response()->json([
            'return_code'=>"SUCCESS"
        ]);
    }
    public function signRecord()
    {
        $uid = getUserToken(Input::get('token'));
        $time = Input::get('date',date('Y-m-d',time()));
        $start = date('Y-m-01 0:0:0',strtotime($time));
        $end = date('Y-m-d 23:59:59', strtotime("$start +1 month -1 day"));
        $sql = getCountSql($uid,$start,$end);
        $data = DB::select($sql);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
    public function UserInfo()
    {
        $uid = getUserToken(Input::get('token'));
        $user = User::find($uid);
        $user->score = $user->score();
        $user->commodities = $user->commodities()->count();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$user
        ]);
    }
    public function getMyCommodities()
    {
//        $uid = getUserToken(Input::get('token'));
        $uid = 1;
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $case = Input::get('case');
        switch ($case){
            case 1:
                $commodities = Commodity::where('user_id','=',$uid)->where('pass','!=','0')
                ->limit($limit)->offset(($page-1)*$limit)->get();
                break;
            case 2:
                $commodities = Commodity::where('user_id','=',$uid)->where('pass','=','0')
                    ->limit($limit)->offset(($page-1)*$limit)->get();
                break;
            case 3:
                $commodities = Commodity::where([
                    'user_id'=>$uid,
                    'pass'=>1,
                    'enable'=>1
                ])->limit($limit)->offset(($page-1)*$limit)->get();
                break;
            case 4:
                $commodities = Commodity::where([
                    'user_id'=>$uid,
                    'pass'=>1,
                    'enable'=>0
                ])->limit($limit)->offset(($page-1)*$limit)->get();
                break;
        };
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$commodities
        ]);
    }
    public function getLevels()
    {
        $levels = MemberLevel::orderBy('level','DESC')->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$levels
        ]);
    }
    public function addLevel()
    {
        $level = new MemberLevel();
//        $level->
    }
}
