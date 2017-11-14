<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\LoginPost;
use App\Http\Requests\RegisterPost;
use App\Http\Requests\ResetPasswordPost;
use App\Models\Attention;
use App\Models\Commodity;
use App\Models\CommodityType;
use App\Models\Description;
use App\Models\DescriptionList;
use App\Models\Member;
use App\Models\MemberLevel;
use App\Models\QQBind;
use App\Models\ScanActivity;
use App\Models\ScanRecord;
use App\Models\Score;
use App\Models\Sign;
use App\Models\SignActivity;
use App\Models\TypeList;
use App\Models\WechatBind;
use App\User;
use function GuzzleHttp\Psr7\uri_for;
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
        $open_id = $request->get('open_id');
        $type = $request->get('type');
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
        $user = new User();
        $user->username = Input::get('username');
        $user->phone = Input::get('phone');
        $user->password = bcrypt($request->get('password'));
        $user->avatar = Input::get('avatar','');
        $inviteCode = Input::get('');
        $inviteUid = getInviteCode($inviteCode);
        if($inviteUid){
            $activity =  ShareActivity::where('end','>',time())->where('state','=','1')->first();
            $user->score = $activity->score;
            $inviteUser = User::find($inviteUid);
            if ($inviteUser){
                $inviteUser -> score += $activity->score;
                $inviteUser->save();
            }
        }
        if ($user->save()){
            if ($open_id){
                if ($type==1){
                    $bind = new QQBind();
                    $bind->user_id = $user->id;
                    $bind->open_id = $open_id;
                    $bind->save();
                }else{
                    $bind = new WechatBind();
                    $bind->user_id = $user->id;
                    $bind->open_id = $open_id;
                    $bind->save();
                }
            }
            $level = MemberLevel::where('level','=',0)->first();
            $member = new Member();
            $member->level = $level->level;
            $member->end_time = $level->time+time();
            $member->send_max = $level->send_max;
            $member->send_daily = $level->send_daily;
            $member->save();
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }


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
                'data' =>[
                    'token'=>$key
                ]
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

    public function modifyPhone()
    {
        $uid = getUserToken(Input::get('token'));
        $code = Input::get('code');
        $number = Input::get('number');
        $data = getCode($number);
        if (empty($data)||$data['type']!='modifyPhone'){
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
        $count = User::where('phone','=',$number)->count();
        if ($count!=0){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'该手机已被绑定！'
            ]);
        }
        $user = User::find($uid);
        $user->phone = $number;
        if ($user->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function resetPassword(ResetPasswordPost $request)
    {
        $phone = $request->get('phone');
        $code = $request->get('code');
        $password = $request->get('password');
        $data = getCode($phone);
        if (empty($data)||$data['type']!='findPassword'){
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
        $user = User::where('phone','=',$phone)->first();
        $user->password = bcrypt($password);
        if ($user->save()){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }

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
        $activity = SignActivity::where('end','>',time())->where('state','=','1')->first();
        if(empty($activity)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'当前没有签到活动！'
            ]);
        }
        $sign = Sign::where('user_id','=',$uid)->where('activity_id','=',$activity->id)->whereDate('created_at', date('Y-m-d',time()))->first();
        if (!empty($sign)){
            return response()->json([
                'return_code'=>"ERROR",
                'return_msg'=>'今天已签到!'
            ]);
        }else{
            $sign = new Sign();
        }
        $sign->user_id = $uid;
        $sign->activity_id = $activity->id;
        $sign->save();
        $user = User::find($uid);
        $user->score += $activity->score;
        $user->save();
        return response()->json([
            'return_code'=>"SUCCESS"
        ]);
    }
    public function signRecord()
    {
        $activity = SignActivity::where('end','>',time())->where('state','=','1')->first();
        if (empty($activity)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'当前没有签到活动！'
            ]);
        }
        $uid = getUserToken(Input::get('token'));
        $time = Input::get('date',date('Y-m-d',time()));
        $start = date('Y-m-01 0:0:0',strtotime($time));
        $end = date('Y-m-d 23:59:59', strtotime("$start +1 month -1 day"));
        $sql = getCountSql($uid,$start,$end);
        $data = DB::select($sql);
        $count = Sign::where([
            'user_id'=>$uid,
            'activity_id'=>$activity->id
        ])->whereMonth('created_at',date('m',time()))->whereYear('created_at',date('Y',time()))->count();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'user_score'=>User::find($uid)->score,
                'current_score'=>$count*$activity->score,
                'signs'=>$data
            ]
        ]);
    }
    public function UserInfo()
    {
        $uid = getUserToken(Input::get('token'));
        $user = User::find($uid);
        $member = Member::where('user_id','=',$user->id)->first();
        if (empty($member)){
            $level = 0;
        }else{
            if ($member->end_time >= time()){
                $level = $member->level;
            }else{
                $level = 0;
            }
        }
        $user->level = $level;
        $user->commodities = $user->commodities()->count();
        $user->wechat = WechatBind::where('user_id','=',$user->id)->count();
        $user->qq = QQBind::where('user_id','=',$user->id)->count();
        $user->qrcode = $user->commodities()->where('pass','=',1)->where('enable','=',1)->count();
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
                $commodities = Commodity::where('user_id','=',$uid)->where('pass','=','1')
                ->limit($limit)->offset(($page-1)*$limit)->get();
                break;
            case 2:
                $commodities = Commodity::where('user_id','=',$uid)->whereIn('pass',[0,2])
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
                $desc = DescriptionList::where('commodity_id','=',$commodities[$i]->id)->pluck('desc_id');
                $desc = Description::whereIn('id',$desc)->pluck('title');
                $commodities[$i]->description =  $desc;
                $commodities[$i]->report_count = $commodities[$i]->report()->count();
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$commodities
        ]);
    }
    public function getLevels()
    {
        $uid = getUserToken(Input::get('token'));
        $member = Member::where('user_id','=',$uid)->first();
        if (empty($member)||$member->end_time < time()){
            $levels = MemberLevel::orderBy('level','DESC')->get();
        }else{
            $levels = MemberLevel::where('level','>',$member->level)->orderBy('level','DESC')->get();
        }

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
        Umeng::android()->sendCustomizedcast($uid,$alias_type,$android_predefined,$customField);
//        dd($data);
        $predefined = [
            'alert'=>[
                'title'=>'title',
                'subtitle'=>'subtitle',
                'body'=>'body'
            ]
        ];
        $data = Umeng::ios()->sendCustomizedcast($uid,$alias_type,$predefined,$customField);
        dd($data);
    }
    public function findUser()
    {
        $phone = Input::get('phone');
        $user = User::where('phone','=',$phone)->first();
        if (empty($user)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'未找到该用户！'
            ]);
        }
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>[
                'user_id'=>$user->id
            ]
        ]);
    }
    public function bindQQ()
    {
        $uid = getUserToken(Input::get('token'));
        $open_id = Input::get('open_id');
        QQBind::where('user_id','=',$uid)->delete();
        $bind = QQBind::where('open_id','=',$open_id)->first();
        if (empty($bind)){
            $bind = new QQBind();
            $bind->user_id = $uid;
            $bind->open_id = $open_id;
            $bind->save();
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }else{
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'该账号已被绑定！'
            ]);
        }
    }
    public function bindWeChat()
    {
        $uid = getUserToken(Input::get('token'));
        $open_id = Input::get('open_id');
        WechatBind::where('user_id','=',$uid)->delete();
        $bind = WechatBind::where('open_id','=',$open_id)->first();
        if (empty($bind)){
            $bind = new WechatBind();
            $bind->user_id = $uid;
            $bind->open_id = $open_id;
            $bind->save();
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }else{
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'该账号已被绑定！'
            ]);
        }
    }
    public function OauthLogin()
    {
        $open_id = Input::get('open_id');
        $type = Input::get('type');
        if ($type==1){
            $bind = QQBind::where('open_id','=',$open_id)->first();
            if (empty($bind)){
                return response()->json([
                    'return_code'=>"FAIL",
                    'return_msg'=>'该QQ没有绑定任何账号!'
                ]);
            }
            $user = User::find($bind->user_id);
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
            $bind = WechatBind::where('open_id','=',$open_id)->first();
            if (empty($bind)){
                return response()->json([
                    'return_code'=>"FAIL",
                    'return_msg'=>'该微信没有绑定任何账号!'
                ]);
            }
            $user = User::find($bind->user_id);
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
        }
    }
    public function OauthSearch()
    {
        $open_id = Input::get('open_id');
        $type = Input::get('type');
        if ($type==1){
            $bind = QQBind::where('open_id','=',$open_id)->first();
        }else{
            $bind = WechatBind::where('open_id','=',$open_id)->first();
        }
        if (!empty($bind)){
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>[
                    'bind'=>1
                ]
            ]);
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'bind'=>0
            ]
        ]);
    }
    public function scan()
    {
        $uid = getUserToken(Input::get('token'));
        $store_id = Input::get('store_id');
        $activity = ScanActivity::where('end','>',time())->where('state','=','1')->first();
        if(empty($activity)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'当前没有扫码活动！'
            ]);
        }
        $scan = ScanRecord::where('user_id','=',$uid)->where('activity_id','=',$activity->id)->whereDate('created_at', date('Y-m-d',time()))->first();
        $count = ScanRecord::where([
            'user_id'=>$uid,
            'activity_id'=>$activity->id,
            'store_id'=>$store_id
        ])->whereMonth('created_at',date('m',time()))->whereYear('created_at',date('Y',time()))->count();
        if (!empty($scan)){
            $user = User::find($uid);
            return response()->json([
                'return_code'=>"SUCCESS",
                'data'=>[
                    'current_score'=>$count*$activity->score,
                    'single_score'=>$activity->score,
                    'do'=>'1',
                    'user_score'=>$user->score
                ]
            ]);
        }else{
            $scan = new ScanRecord();
            $scan->user_id = $uid;
            $scan->activity_id = $activity->id;
            $scan->store_id = $store_id;
            $scan->save();
            $user = User::find($uid);
            $user->score += $activity->score;
            $user->save();
            return response()->json([
                'return_code'=>"SUCCESS",
                'data'=>[
                    'current_score'=>$count*$activity->score,
                    'single_score'=>$activity->score,
                    'do'=>0,
                    'user_score'=>$user->score
                ]
            ]);
        }
    }
    public function getAllUsers()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $username = Input::get('username');
        $user_id = Input::get('user_id');
        $level = Input::get('level');
        $start = Input::get('start');
        $end = Input::get('end');
        $userDB = DB::table('users');
        $count = $userDB->count();
        if ($user_id){
            $userDB->where('id','=',$user_id);
            $count = $userDB->count();
        }
        if ($username){
             $userDB->where('username','like','%'.$username.'%');
            $count = $userDB->count();
        }
        if ($level){
            $id = Member::where('level','=',$level)->pluck('user_id');
             $userDB->whereIn('id',$id);
            $count = $userDB->count();
        }
        if ($start){
            $userDB->where('created_at','>',$start)->where('created_at','<',$end);
            $count = $userDB->count();
        }
        $data = $userDB->limit($limit)->offset(($page-1)*$limit)->get();
        if (!empty($data)){
            for ($i=0;$i<count($data);$i++){
                $data[$i]->member = Member::where('user_id','=',$data[$i]->id)->first();
                $data[$i]->commodity_count = $data[$i]->commodities()->count();
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data,
            'count'=>$count
        ]);
    }
    public function modifyUser($id)
    {
        $user = User::find($id);
        $state = Input::get('state');
        if ($state==0){
            $user->state = 0;
        }else{
            $user->state = 1;
        }
        $user->save();
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);
    }
}
