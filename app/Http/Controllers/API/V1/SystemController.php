<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\ReportPost;
use App\Models\Advert;
use App\Models\Article;
use App\Models\City;
use App\Models\Commodity;
use App\Models\Description;
use App\Models\DescriptionList;
use App\Models\MemberLevel;
use App\Models\Message;
use App\Models\PartTime;
use App\Models\Qrcode;
use App\Models\RefuseReasen;
use App\Models\Report;
use App\Models\ReportReason;
use App\Models\ScanActivity;
use App\Models\ShareActivity;
use App\Models\SignActivity;
use App\Models\SysConfig;
use App\Models\UserGuide;
use App\User;
use DeepCopy\f001\A;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Zizaco\Entrust\Entrust;
use Zizaco\Entrust\EntrustPermission;
use Zizaco\Entrust\EntrustRole;

class SystemController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * 添加会员等级
     */
    public function addMemberLevel()
    {
        $id = Input::get('id');
        $level = Input::get('level');
        if (empty($id)){
            $member = new MemberLevel();
            $count = MemberLevel::where('level','=',$level)->count();
            if ($count>=1){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>'不能添加重复的会员等级'
                ]);
            }
            $member->level = Input::get('level');
//            $member->title = Input::get('title');
            $member->description = Input::get('description');
            $member->price = Input::get('price');
            $member->time = (Input::get('time'))*3600;
            $member->send_daily = Input::get('send_daily');
            $member->send_max = Input::get('send_max');
        }else{
            $member = MemberLevel::find($id);
            $count = MemberLevel::where('id','!=',$id)->where('level','=',$level)->count();
            if ($count>=1){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>'不能添加重复的会员等级'
                ]);
            }
            $member->level = Input::get('level');
//            $member->title = Input::get('title');
            $member->description = Input::get('description');
            $member->price = Input::get('price');
            $member->time = (Input::get('time'))*3600;
            $member->send_daily = Input::get('send_daily');
            $member->send_max = Input::get('send_max');
        }

        if ($member->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取会员等级列表
     */
    public function getMemberLevels()
    {
        $level = MemberLevel::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$level
        ]);
    }

    public function getMemberLevel($id)
    {
        $level = MemberLevel::find($id);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$level
        ]);
    }
    public function delMemberLevel($id)
    {
        $level = MemberLevel::find($id);
        if ($level->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function addRole()
    {
        $role = new EntrustRole();
        $role->name = Input::get('name');
        $role->display_name = Input::get('display_name');
        $role->description = Input::get('description');
        if ($role->save()){
            $pres = Input::get('pres');
            if (!empty($pres)){
                for ($i=0;$i<count($pres);$i++){
                    $permission = EntrustPermission::find($pres[$i]);
                    $role->attachPermission($permission);
                }
            }
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delRole($id)
    {
        $role = EntrustRole::find($id);
        $role->delete(); // This will work no matter what
// Force Delete
        $role->users()->sync([]); // Delete relationship data
        $role->perms()->sync([]); // Delete relationship data
        $role->forceDelete();
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);
    }
    public function getRoles()
    {
        $roles = EntrustRole::all();
        $count = EntrustRole::count();
        for ($i=0;$i<count($roles);$i++){
            $roles[$i]->perms = $roles[$i]->perms()->get();
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
            'data'=>$roles
        ]);
    }
    public function getRole($id)
    {
        $roles = EntrustRole::find($id);
        $permission = $roles->perms()->get();
        $user = $roles->users()->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'role'=>$roles,
                'permission'=>$permission,
                'user'=>$user
            ]
        ]);
    }
    public function getPermissions()
    {
        $permissions = EntrustPermission::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$permissions
        ]);
    }
    public function addPermission()
    {
        $permission = new EntrustPermission();
        $permission->name = Input::get('name');
        $permission->display_name = Input::get('display_name');
        $permission->description = Input::get('description');
        if ($permission->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function attachPermission()
    {

    }
    public function test()
    {
        $user = User::find(7);
        if ($user->can('addRole')){
            return "YES";
        }
        return "NO";
    }
    public function getAllCities()
    {
        $cities = City::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$cities
        ]);
    }
    public function getCities()
    {
        $pid = Input::get('pid');
        if(!$pid){
            $cities = City::where('pid','=',0)->get();
        }else{
            $cities = City::where('pid','=',$pid)->get();
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$cities
        ]);
    }
    public function setCity()
    {
        $cities = City::where('pid','=',0)->get();
        for ($i=0;$i<count($cities);$i++){
                $cities[$i]->cities = City::where('pid','=',$cities[$i]->id)->get();
        }
        return response()->json($cities);
    }
    public function getCityInfo($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if($output === FALSE ){
            return false;
        }
        curl_close($curl);
        return json_decode($output,JSON_UNESCAPED_UNICODE);
    }
    public function getMessages()
    {
        $uid = getUserToken(Input::get('token'));
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $messages = Message::where('receive_id','=',$uid)->limit($limit)->offset(($page-1)*$limit)->orderBy('read','ASC')->orderBy('id','DESC')->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$messages
        ]);
    }
    public function getMessage($id)
    {
        $message = Message::find($id);
        $message->read = 1;
        $message->save();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$message
        ]);
    }
    public function readMessage($id)
    {
        $message = Message::find($id);
        $message->read = 1;
        $message->save();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$message
        ]);
    }
    public function delMessage($id)
    {
        $uid = getUserToken(Input::get('token'));
        $message = Message::find($id);
        if ($message->receive_id != $uid){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'无权操作！'
            ]);
        }
        if ($message->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    public function addAdvert()
    {
        $id = Input::get('id');
        if ($id){
            $advert = Advert::find($id);
        }else{
            $advert = new Advert();
        }
        $advert->title = Input::get('title');
        $advert->city_id = Input::get('city_id');
        $advert->url = Input::get('url');
        $advert->link_url = Input::get('link_url');
        $advert->type = Input::get('type');
        $advert->parents = Input::get('parents');
        $advert->state = Input::get('state',0);
        if ($advert->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getAdverts()
    {
        $type = Input::get('type');
        $city_id = Input::get('city_id');
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $adverts = Advert::where([
            'type'=>$type,
            'city_id'=>$city_id,
            'state'=>'1'
        ])->orderByRaw('Rand()')->limit($limit)->offset(($page-1)*$limit)->first();
        if (empty($adverts)){
            $pid = City::where('id','=',$city_id)->pluck('pid')->first();
            $adverts = Advert::where([
                'type'=>$type,
                'city_id'=>$pid,
                'state'=>'1'
            ])->orderByRaw('Rand()')->limit($limit)->offset(($page-1)*$limit)->first();
        }
        if (empty($adverts)){
            $adverts = Advert::where([
                'type'=>$type,
                'city_id'=>0,
                'state'=>'1'
            ])->orderByRaw('Rand()')->limit($limit)->offset(($page-1)*$limit)->first();
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$adverts
        ]);
    }

    public function getAllAdverts()
    {
//        $type = Input::get('type');
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $adverts = Advert::limit($limit)->offset(($page-1)*$limit)->get();
        if(!empty($adverts)){
            for ($i=0;$i<count($adverts);$i++){
                $adverts[$i]->city = $adverts[$i]->city()->first();
            }
        }
        $count = Advert::count();
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
            'data'=>$adverts
        ]);
    }
    public function delAdvert($id)
    {
        $advert = Advert::find($id);
        if ($advert->delete()){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }
    }
    public function getQrCode()
    {
        $qrcode = Qrcode::first();
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>$qrcode
        ]);
    }
    public function addQrCode($id)
    {
        $qrcode = Qrcode::find($id);
        if (empty($qrcode)){
            $qrcode = new Qrcode();
        }
        $qrcode->logo = Input::get('logo');
        $qrcode->content = Input::get('content');
        $qrcode->name = Input::get('name');
        if ($qrcode->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function addReportReason()
    {
        $id = Input::get('id');
        if ($id){
            $reason = ReportReason::find($id);
        }else{
            $reason = new ReportReason();
        }
        $reason->content = Input::get('content');
        if ($reason->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getReportReasons()
    {
        $reasons = ReportReason::all();
        $count = ReportReason::count();
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
            'data'=>$reasons
        ]);
    }
    public function addUserGuide()
    {
        $id = Input::get('id');
        if ($id){
            $guide = UserGuide::find($id);
        }else{
            $guide = new UserGuide();
        }
        $guide->title = Input::get('title');
        $guide->content = Input::get('content');
        $guide->sort = Input::get('sort',1);
        if ($guide->save()){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }
    }
    public function getUserGuides()
    {
        $guides = UserGuide::orderBy('sort','DESC')->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$guides
        ]);
    }
    public function delUserGuides($id)
    {
        $guide = UserGuide::find($id);
        if ($guide->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getSystemConfig()
    {
        $config = SysConfig::first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$config
        ]);
    }
    public function modifySystemConfig()
    {
        $config = SysConfig::first();
        $config->need_pay = Input::get('need_pay');
        $config->pic_score = Input::get('pic_score');
        $config->phone_score = Input::get('phone_score');
        $config->pic_price = Input::get('pic_price');
        $config->phone_price = Input::get('phone_price');
        $config->show_sign = Input::get('show_sign');
        $config->show_qrcode = Input::get('show_qrcode');
        $config->show_share = Input::get('show_share');
        $config->apply = Input::get('apply');
        $config->save();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$config
        ]);
    }

    public function getArticles()
    {
        $articles = Article::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$articles
        ]);
    }
    public function addArticle()
    {
        $type = Input::get('type');
        $article = Article::where('type','=',$type)->first();
        if (empty($article)){
            $article = new Article();
        }
        $article -> content = Input::get('content');
        $article -> title = Input::get('title');
        if ($article->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getArticle()
    {
        $type = Input::get('type');
        $article = Article::where('type','=',$type)->first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$article
        ]);
    }
    public function getReports()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $state = Input::get('state');
        $reports = DB::table('reports')->where('state','!=','2');
        $count = $reports->count();
        $username = Input::get('username');
        $user_id = Input::get('user_id');
        if ($username){
            $user = User::where('username','like','%'.$username.'%')->pluck('id');
            $reports->whereIn('user_id',$user);
            $count = $reports->count();
        }
        if ($user_id){
            $reports->where('user_id','=',$user_id);
            $count = $reports->count();
        }
        if($state){
            $reports->where('state','=',$state);
            $count = $reports->count();
        }
        $data = $reports->limit($limit)->offset(($page-1)*$limit)->get();
        if (!empty($data)){
            for ($i=0;$i<count($data);$i++) {
                $commodity = Commodity::find($data[$i]->commodity_id);
                if (!empty($commodity)){
                    $commodity->pictures = $commodity->pictures()->get();
                    $list = DescriptionList::where('commodity_id','=',$commodity->id)->pluck('desc_id');
                    $commodity->descriptions = Description::whereIn('id',$list)->pluck('title');
                    $data[$i]->commodity = $commodity;
                    $data[$i]->read_number = $commodity->read_number;
                    $data[$i]->report_number = $commodity->report()->count();
                }
                $user = User::find($data[$i]->user_id);
                $data[$i]->username = empty($user)?'':$user->username;
                $reason = $data[$i]->type_id;
                $reason = explode(',',$reason);
                $data[$i]->reports = ReportReason::whereIn('id',$reason)->pluck('content');
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
            'data'=>$data
        ]);

    }
    public function modifyReport($id)
    {
        $state = Input::get('state');
        $report = Report::find($id);
        $report->state = $state;
        if ($report->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function listPartTime()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $state = Input::get('state');
        $parttime = DB::table('part_times');
        $count = $parttime->count();
        if (isset($state)){
            $parttime->where('state','=',$state);
            $count  = $parttime->count();
        }
        $data = $parttime->limit($limit)->offset(($page-1)*$limit)->orderBy('state','ASC')->orderBy('id','DESC')->get();
        return response()->json([
            'return_code'=>"SUCCESS",
            'count'=>$count,
            'data'=>$data
        ]);
    }
    public function modifyPartTime($id)
    {
        $state = Input::get('state');
        $parttime = PartTime::find($id);
        $parttime->state = $state;
        if ($parttime->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getSignActivity()
    {
        $activity = SignActivity::first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$activity
        ]);
    }
    public function addSignActivity()
    {
        $activity = SignActivity::first();
        if (empty($activity)){
            $activity = new SignActivity();
        }
        $activity->start = strtotime(Input::get('start'));
        $activity->end = strtotime(Input::get('end'));
        $activity->score = Input::get('score');
        if ($activity->save()){
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>$activity
            ]);
        }
    }
    public function getScanActivity()
    {
        $activity = ScanActivity::first();
        if (empty($activity)){
            $activity = new ScanActivity();
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$activity
        ]);
    }
    public function addScanActivity()
    {
        $activity = ScanActivity::first();
        if (empty($activity)){
            $activity = new ScanActivity();
        }
        $activity->start = strtotime(Input::get('start'));
        $activity->end = strtotime(Input::get('end'));
        $activity->score = Input::get('score');
        if ($activity->save()){
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>$activity
            ]);
        }
    }
    public function addRefuseReasen()
    {
        $id = Input::get('id');
        if ($id){
            $refuse = RefuseReasen::find($id);
        }else{
            $refuse = new RefuseReasen();
        }
        $refuse->title = Input::get('title');
        $refuse->content = Input::get('content');
        if ($refuse->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getRefuseReasons()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $refuse = RefuseReasen::limit($limit)->offset(($page-1)*$limit)->get();
        $count = RefuseReasen::count();
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
            'data'=>$refuse
        ]);
    }
    public function getShareActivity()
    {
        $uid = getUserToken(Input::get('token'));
        $activity = ShareActivity::where('end','>',time())->where('state','=','1')->first();
        if (empty($activity)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'当前没有活动！'
            ]);
        }else{
            $code = createNonceStr(5);
            $code .=$uid;
            $time = $activity->end - time();
//            setUserToken($code,$uid);
            setInviteCode($code,$uid,$time);
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>[
                    'start'=>date('m-d H:i',$activity->start),
                    'end'=>date('m-d H:i',$activity->end),
                    'rule'=>$activity->rule,
                    'score'=>8,
                    'link'=>formatUrl('activity/'.$code),
                ]
            ]);
        }
    }
    public function delRefuseReason($id)
    {
        $reason = RefuseReasen::find($id);
        if ($reason->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delReportReason($id)
    {
        $reason = ReportReason::find($id);
        if ($reason->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getShareActivities()
    {
        $activity = ShareActivity::first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$activity
        ]);
    }
    public function addShareActivity()
    {

        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        if ($start>$end || $end<time()){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'时间错误！'
            ]);
        }
        $activity = ShareActivity::first();
        $activity->start = $start;
        $activity->end = $end;
        $activity->score = Input::get('score');
        $activity->content = Input::get('content');
        $activity->rule = Input::get('rule');
        $activity->type = Input::get('type');
        $activity->daily_max = Input::get('daily_max');
        if ($activity->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function showStore($id)
    {
        $user = User::find($id);
        $count = $user->commodities()->where([
            'pass'=>1,
            'enable'=>1
        ])->count();
        $commodities = $user->commodities()->where([
            'pass'=>1,
            'enable'=>1
        ])->limit(5)->get();
        return view('store',[
            'user'=>$user,
            'count'=>$count,
            'commodities'=>$commodities
        ]);
    }
    public function showShareActivity($code)
    {
        $activity = ShareActivity::where('state','=','1')->first();
        return view('share',[
            'code'=>$code
        ]);
    }
}
