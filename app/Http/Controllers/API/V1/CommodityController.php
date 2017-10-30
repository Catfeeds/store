<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\CommodityPost;
use App\Http\Requests\FilterPost;
use App\Http\Requests\PartTimePost;
use App\Http\Requests\RejectPost;
use App\Http\Requests\ReportPost;
use App\Models\Attention;
use App\Models\Collect;
use App\Models\Commodity;
use App\Models\CommodityPicture;
use App\Models\CommodityType;
use App\Models\Member;
use App\Models\PartTime;
use App\Models\Reject;
use App\Models\Report;
use App\Models\SysConfig;
use App\Models\TypeList;
use App\Models\UserBuy;
use App\User;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use League\Flysystem\Config;

class CommodityController extends Controller
{
    //
    public function addReport(ReportPost $request)
    {
        $report = new Report();
        $report->user_id = getUserToken($request->get('token'));
        $report->commodity_id = $request->get('commodity_id');
        $report->type_id =  $request->get('type_id');
        $report->phone = $request->get('phone');
        $report->contact = $request->get('contact');
        $report->description = $request->get('description');
        if ($report->save()){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }
    }

    public function getCommodity($id)
    {
        $commodity = Commodity::find($id);
        if (empty($commodity)){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'没找到该消息!'
            ]);
        }
        $commodity->read();
        $needPay = SysConfig::first();
        if ($needPay->need_pay){
            $uid = getUserToken(Input::get('token'));
            if (!$uid){
                $commodity->phone = '***********';
                $commodity->pictures =[];
            }else{
                if ($commodity->user_id == $uid){
                    $list = TypeList::where('commodity_id','=',$commodity->id)->pluck('type_id');
                    $commodity->type = CommodityType::whereIn('id',$list)->get();
                    $commodity->pictures = $commodity->pictures()->get();
                    return response()->json([
                        'return_code'=>'SUCCESS',
                        'data'=>$commodity
                    ]);
                }
                $member = Member::where('user_id','=',$uid)->first();
                if (empty($member)||$member->end_time<time()){
                    $record = UserBuy::where('user_id','=',$uid)->where('commodity_id','=',$id)->first();
                    if (empty($record)){
                        $commodity->phone = '***********';
                        $commodity->pictures =[];
                    }else{
                        $commodity->phone = ($record->phone==1)?$commodity->phone:'***********';
                        if ($record->pic==1){
                            $commodity->pictures = $commodity->pictures()->get();
                        }
                    }
                }else{
                    $commodity->pictures = $commodity->pictures()->get();
                }
            }
        }else{
            $commodity->pictures = $commodity->pictures()->pluck('thumb_url');
        }
        $list = TypeList::where('commodity_id','=',$commodity->id)->pluck('type_id');
        $commodity->type = CommodityType::whereIn('id',$list)->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$commodity
        ]);
    }

    public function getCommodities(FilterPost $filterPost)
    {
        $latitude = $filterPost->get('latitude');
        $longitude = $filterPost->get('longitude');
        $radius = $filterPost->get('radius');
        $fixdata = getAround($latitude,$longitude,$radius*1000);
        $type = $filterPost->get('type');
        if (isset($type)){
            $category_id = TypeList::where('type_id','=',$type)->pluck('commodity_id');
            $commodities = Commodity::whereIn('id',$category_id)->where([
                'pass'=>1,
                'enable'=>1
            ])->whereBetween('latitude',[$fixdata['minLat'],$fixdata['maxLat']])->whereBetween('longitude',[$fixdata['minLng'],$fixdata['maxLng']])->get();
        }else{
            $commodities = [];
        }
        if (!empty($commodities)){
            $length = count($commodities);
            for ($i=0;$i<$length;$i++){
                $type = TypeList::where('commodity_id','=',$commodities[$i]->id)->pluck('type_id');
                $title = CommodityType::whereIn('id',$type)->pluck('title');
                $commodities[$i]->type = empty($title)?'':$title;
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$commodities
        ]);
    }
    public function getCommodityTypes()
    {
        $commodityTypes = CommodityType::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$commodityTypes
        ]);
    }
    public function addCommodity(CommodityPost $commodityPost)
    {
        $id = $commodityPost->get('id');
        $uid = getUserToken(Input::get('token'));
        if (isset($id)){
            $commodity = Commodity::find($id);
            if (empty($commodity)){
                return response()->json([
                    'return_code'=>"FAIL",
                    'return_msg'=>'没找到该消息!'
                ]);
            }else{
                if ($commodity->user_id !=$uid){
                    return response()->json([
                        'return_code'=>"FAIL",
                        'return_msg'=>'无权修改该信息!'
                    ]);
                }
                $commodity->type = $commodityPost->get('type');
                $commodity->title = $commodityPost->get('title');
                $commodity->price = $commodityPost->get('price');
                $commodity->description = $commodityPost->get('description');
                $commodity->detail = $commodityPost->get('detail',null);
                $commodity->phone = $commodityPost->get('phone');
                $commodity->QQ = $commodityPost->get('qq',null);
                $commodity->WeChat = $commodityPost->get('wechat',null);
                $commodity->latitude = $commodityPost->get('latitude');
                $commodity->longitude = $commodityPost->get('longitude');
                $commodity->address = $commodityPost->get('address');
                $commodity->pass = 0;
            }
        }else{
            $commodity = new Commodity();
            $commodity->title = $commodityPost->get('title');
            $commodity->price = $commodityPost->get('price');
            $commodity->description = $commodityPost->get('description');
            $commodity->detail = $commodityPost->get('detail',null);
            $commodity->phone = $commodityPost->get('phone');
            $commodity->QQ = $commodityPost->get('qq',null);
            $commodity->WeChat = $commodityPost->get('wechat',null);
            $commodity->latitude = $commodityPost->get('latitude');
            $commodity->longitude = $commodityPost->get('longitude');
            $commodity->address = $commodityPost->get('address');
            $commodity->user_id = $uid;
//            $commodity->type = $commodityPost->get('type');
        }
        if ($commodity->save()){
            $type = $commodityPost->get('type');
            if (!empty($type)){
                TypeList::where('commodity_id','=',$commodity->id)->delete();
                for($i=0;$i<count($type);$i++){
                    $list = new TypeList();
                    $list->commodity_id = $commodity->id;
                    $list->type_id = $type[$i];
                    $list->save();
                }
            }
            $pic = $commodityPost->get('pic');
            if(!empty($pic)){
                CommodityPicture::whereIn('id',$pic)->update([
                    'commodity_id'=>$commodity->id
                ]);
            }
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    public function deleteCommodity($id)
    {
        $uid = getUserToken(Input::get('token'));
        $commodity = Commodity::find($id);
        if (empty($commodity)){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'没找到该消息!'
            ]);
        }
        if ($commodity->user_id !=$uid){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'无权修改该信息!'
            ]);
        }
        if ($commodity->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    public function addReportReject(RejectPost $rejectPost)
    {
        $reject = new Reject();
        $reject->commodity_id = $rejectPost->get('commodity_id');
        $reject->detail = $rejectPost->get('detail');
        $reject->phone = $rejectPost->get('phone');
        if ($reject->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function addPicture(Request $request)
    {
        $destinationPath = 'uploads';
        $file_name = $request->get('file_name');
        $pic = new CommodityPicture();
        $pic->title = $request->get('title');
        $pic->base_url = $destinationPath.'/'.$file_name;
        $pic->thumb_url = formatUrl($destinationPath.'/thumb_'.$file_name);
        $pic->url = formatUrl($destinationPath.'/'.$file_name);
        if ($pic->save()){
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>$pic
            ]);
        }
    }
    public function delPicture()
    {

    }
    public function addPartTime(PartTimePost $partTimePost)
    {
        $uid = getUserToken($partTimePost->get('token'));
        $partTime = new PartTime();
        $partTime->user_id = $uid;
        $partTime->name = $partTimePost->get('name');
        $partTime->sex = $partTimePost->get('sex');
        $partTime->area = $partTimePost->get('area');
        $partTime->time = $partTimePost->get('time');
        $partTime->number = $partTimePost->get('number');
        if ($partTime->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function addCollect()
    {
        $uid = getUserToken(Input::get('token'));
        $commodity_id = Input::get('commodity_id');
        $collect = new Collect();
        $collect->user_id = $uid;
        $collect->commodity_id = $commodity_id;
        if ($collect->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delCollect($id)
    {
        $collect = Collect::find($id);
        if ($collect->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getCollects()
    {
        $uid = getUserToken(Input::get('token'));
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $collects = Collect::where('user_id','=',$uid)->pluck('commodity_id')->toArray();
        $commodities = Commodity::whereIn('id',$collects)->limit($limit)->offset(($page-1)*$limit)->get();
        $this->formatCollects($commodities);
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>$commodities
        ]);
    }
    public function formatCollects(&$collects)
    {
        $length = count($collects);
        if ($length==0){
            return [];
        }
        for ($i=0;$i<$length;$i++){
            $type = TypeList::where('commodity_id','=',$commodities[$i]->id)->pluck('type_id');
            $title = CommodityType::whereIn('id',$type)->pluck('title');
            $commodities[$i]->type = empty($title)?'':$title;
            $picture = $commodities[$i]->pictures()->pluck('thumb_url')->first();
            $commodities[$i]->picture = empty($picture)?'':$picture;
        }

    }
    public function addAttention()
    {
        $uid = getUserToken(Input::get('token'));
        $attention = new Attention();
        $attention->user_id = $uid;
        $attention->attention_id = Input::get('attention_id');
        if ($attention->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delAttention($id)
    {
        $attention = Attention::find($id);
        if ($attention->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getAttentions()
    {
        $uid = getUserToken(Input::get('token'));
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $attentions = Attention::where('user_id','=',$uid)->limit($limit)->offset(($page-1)*$limit)->get();
        $this->formatAttentions($attentions);
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>$attentions
        ]);
    }
    public function formatAttentions(&$attentions)
    {
        $length = count($attentions);
        if ($length==0){
            return [];
        }
        for ($i=0;$i<$length;$i++){
            $user = User::find($attentions[$i]->attention_id);
            $attentions[$i]->name = $user->name;
            $attentions[$i]->create_time = date('Y-m-d',strtotime($user->created_at));
            $attentions[$i]->avatar = $user->avatar;
            $member = Member::where('user_id','=',$user->id)->orderBy('id','DESC')->first();
            if (empty($member)){
                $attentions[$i]->level = 0;
            }else{
                if ($member->end_time>=time()){
                    $attentions[$i]->level = $member->level;
                }else{
                    $attentions[$i]->level = 0;
                }
            }
        }

    }
    public function getStore($id)
    {
        $user = User::find($id);
        $member = Member::where('user_id','=',$id)->orderBy('id','DESC')->first();
        if (empty($member)){
            $level = 0;
        }else{
            if ($member->end_time>=time()){
                $level = $member->level;
            }else{
                $level = 0;
            }
        }
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $commodity = Commodity::where([
            'user_id'=>$id,
            'enable'=>1,
            'pass'=>1
        ])->limit($limit)->offset(($page-1)*$limit)->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'name'=>$user->name,
                'create_time'=>date('Y-m-d',strtotime($user->created_at)),
                'avatar'=>$user->avatar,
                'level'=>$level,
                'commodities'=>$commodity
            ]
        ]);
    }
}
