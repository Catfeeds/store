<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\LoginPost;
use App\Http\Requests\RegisterPost;
use App\Http\Requests\ResetPasswordPost;
use App\Models\Attention;
use App\Models\Commodity;
use App\Models\CommodityType;
use App\Models\Member;
use App\Models\MemberLevel;
use App\Models\Score;
use App\Models\Sign;
use App\Models\TypeList;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use League\Flysystem\Config;
use Symfony\Component\CssSelector\Parser\Token;
use Zizaco\Entrust\EntrustRole;
use Zzl\Umeng\Facades\Umeng;

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
            ]);
        }
        if ($data['code']!=$code){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'验证码错误！'
            ]);
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
                ]);
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
                    ]);
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
                ]);
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
        $push = new Umeng();
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
        $member = Member::where('user_id','=',$user->id)->orderBy('id','DESC')->first();
        if (empty($member)){
            $level = 0;
        }else{
            if ($member->end_time>=time()){
                $level = $member->level;
            }else{
                $level = 0;
            }
        }
        $user->level = $level;
        $user->commodities = $user->commodities()->count();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$user
        ]);
    }
    public function getMyCommodities()
    {
        $uid = getUserToken(Input::get('token'));
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
        if (!empty($commodities)){
            $length = count($commodities);
            for ($i=0;$i<$length;$i++){
//                $type = TypeList::where('commodity_id','=',$commodities[$i]->id)->pluck('type_id');
                $title = CommodityType::find($commodities[$i]->type);
                $commodities[$i]->type = empty($title)?'':$title->title;
                $picture = $commodities[$i]->pictures()->pluck('thumb_url')->first();
                $commodities[$i]->picture = empty($picture)?'':$picture;
            }
        }
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

    }
    public function adminLogin()
    {
        $username = Input::get('username');
        $password = Input::get('password');
        if (Auth::attempt(['username'=>$username,'password'=>$password],true)){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }else{
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'用户不存在或密码错误！'
            ]);
        }
    }
    public function getPermissions()
    {
        $user = Auth::user();
        $role = $user->roles();
        $permissions = $role->prns();
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>$permissions
        ]);
    }
    public function addRole()
    {
        $role = new EntrustRole();
        $role->users();
    }
    public function setUserInfo()
    {
        $uid = getUserToken(Input::get('token'));
        $user = User::find($uid);
        $name = Input::get('name');
        $avatar = Input::get('avatar');
        $user->name = empty($name)?$user->name:$name;
        $user->avatar = empty($avatar)?$user->avatar:$avatar;
        if ($user->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    public function push()
    {
        $uid = Input::get('uid');
        $alias_type = Input::get('alias_type');
        $android_predefined = [
            'ticker' => 'android ticker',
            'title' => 'Test Push',
            'text' => 'Test Text',
            'play_vibrate' => 'true',
            'play_lights' => 'true',
            'play_sound' => 'true',
            'after_open' => 'go_activity',
            'activity' => 'com.sennki.flybrid.main.user.UserMyMessageActivity'
        ];
        $customField = array(); //oth
        $data =Umeng::android()->sendCustomizedcast($uid,$alias_type,$android_predefined,$customField);
        dd($data);
//        Umeng::ios()->sendCustomizedcast($uid,$alias_type,$predefined,$customField);
    }
}
