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
use App\Models\Description;
use App\Models\DescriptionList;
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
        $fixdata = getAround($commodity->latitude,$commodity->longitude,500);
        $commodities = Commodity::where('id','!=',$id)->where([
            'pass'=>1,
            'enable'=>1
        ])->whereBetween('latitude',[$fixdata['minLat'],$fixdata['maxLat']])->whereBetween('longitude',[$fixdata['minLng'],$fixdata['maxLng']])->select(['id','type','latitude','longitude'])->orderByRaw('RAND()')->limit(4)->get();
        $data = $this->formatCommodities($commodities,$commodity->latitude,$commodity->longitude);
        $commodity->around = $data;
        if ($needPay->need_pay){
            $uid = getUserToken(Input::get('token'));
            if (!$uid){
                $commodity->phone = '***********';
                $commodity->pictures =$commodity->pictures()->first();
                $commodity->collect = 0;
                $list = DescriptionList::where('commodity_id','=',$commodity->id)->pluck('desc_id');
                $commodity->description = Description::whereIn('id',$list)->get();
                $commodity->type = CommodityType::find($commodity->type);
            }else{
                $collect = Collect::where([
                    'user_id'=>$uid,
                    'commodity_id'=>$id
                ])->count();
                $commodity->collect = $collect;
                if ($commodity->user_id == $uid){

//                    $list = TypeList::where('commodity_id','=',$commodity->id)->pluck('type_id');
                    $list = DescriptionList::where('commodity_id','=',$commodity->id)->pluck('desc_id');
                    $commodity->description = Description::whereIn('id',$list)->get();
                    $commodity->type = CommodityType::find($commodity->type);
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
            $commodity->pictures = $commodity->pictures()->get();
        }
        $list = DescriptionList::where('commodity_id','=',$commodity->id)->pluck('desc_id');
        $commodity->description = Description::whereIn('id',$list)->get();
        $commodity->type = CommodityType::find($commodity->type);
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
//            $category_id = TypeList::where('type_id','=',$type)->pluck('commodity_id');
            $commodities = Commodity::where('type','=',$type)->where([
                'pass'=>1,
                'enable'=>1
            ])->whereBetween('latitude',[$fixdata['minLat'],$fixdata['maxLat']])->whereBetween('longitude',[$fixdata['minLng'],$fixdata['maxLng']])->get();
        }else{
            $commodities = [];
        }
        if (!empty($commodities)){
            $length = count($commodities);
            for ($i=0;$i<$length;$i++){
                $desc = DescriptionList::where('commodity_id','=',$commodities[$i]->id)->pluck('desc_id');
                $title = Description::whereIn('id',$desc)->pluck('title');
                $commodities[$i]->description = $title;
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
                if ($commodity->user_id != $uid){
                    return response()->json([
                        'return_code'=>"FAIL",
                        'return_msg'=>'无权修改该信息!'
                    ]);
                }
                $commodity->type = $commodityPost->get('type');
                $commodity->title = $commodityPost->get('title');
                $commodity->price = $commodityPost->get('price');
//                $commodity->description = $commodityPost->get('description');
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
//            $commodity->description = $commodityPost->get('description');
            $commodity->detail = $commodityPost->get('detail',null);
            $commodity->phone = $commodityPost->get('phone');
            $commodity->QQ = $commodityPost->get('qq',null);
            $commodity->WeChat = $commodityPost->get('wechat',null);
            $commodity->latitude = $commodityPost->get('latitude');
            $commodity->longitude = $commodityPost->get('longitude');
            $commodity->address = $commodityPost->get('address');
            $commodity->user_id = $uid;
            $commodity->type = $commodityPost->get('type');
        }
        if ($commodity->save()){
            $description = $commodityPost->get('description');
            if (!empty($description)){
                DescriptionList::where('commodity_id','=',$commodity->id)->delete();
                for($i=0;$i<count($description);$i++){
                    $list = new DescriptionList();
                    $list->commodity_id = $commodity->id;
                    $list->desc_id = $description[$i];
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
    public function addPicture($id)
    {
        $pic =CommodityPicture::find($id);
        if ($pic->commodtity_id != 0){
            $uid = getUserToken(Input::get('token'));
            $commodtity = Commodity::find($pic->commodtity_id);
            if ($commodtity->user_id!=$uid){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_message'=>'无权操作！'
                ]);
            }
        }
        $pic->title = Input::get('title');
        if ($pic->save()){
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>$pic
            ]);
        }
    }
    public function delPicture($id)
    {
        $pic = CommodityPicture::find($id);
        if ($pic->commodtity_id != 0){
            $uid = getUserToken(Input::get('token'));
            $commodtity = Commodity::find($pic->commodtity_id);
            if ($commodtity->user_id!=$uid){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_message'=>'无权操作！'
                ]);
            }
        }
        if ($pic->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
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
        $partTime->front = $partTimePost->get('front');
        $partTime->back = $partTimePost->get('back');
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
        $collect = Collect::where([
            'user_id'=>$uid,
            'commodity_id'=>$commodity_id
        ])->first();
        if (!empty($collect)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'已收藏的不能重复收藏！'
            ]);
        }
        $collect = new Collect();
        $collect->user_id = $uid;
        $collect->commodity_id = $commodity_id;
        if ($collect->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delCollect()
    {
        $uid = getUserToken(Input::get('token'));
        $commodity_id = Input::get('commodity_id');
        $collect = Collect::where([
            'user_id'=>$uid,
            'commodity_id'=>$commodity_id
        ])->first();
        if (empty($collect)){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
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
        $collects = Collect::where('user_id','=',$uid)->pluck('commodity_id');
        $commodities = Commodity::whereIn('id',$collects)->limit($limit)->offset(($page-1)*$limit)->get();
        //dd($commodities);
        $this->formatCollects($commodities);
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>$commodities
        ]);
    }
    public function formatCollects(&$commodities)
    {
        $length = count($commodities);
        if ($length==0){
            return [];
        }
        for ($i=0;$i<$length;$i++){
//            $type = TypeList::where('commodity_id','=',$commodities[$i]->id)->pluck('type_id');
            $title = CommodityType::find($commodities[$i]->type)->title;
            $commodities[$i]->type = empty($title)?'':$title;
            $picture = $commodities[$i]->pictures()->pluck('thumb_url')->first();
            $commodities[$i]->picture = empty($picture)?'':$picture;
        }

    }
    public function addAttention()
    {
        $uid = getUserToken(Input::get('token'));
        $attention_id = Input::get('attention_id');
        $attention = Attention::where([
            'user_id'=>$uid,
            'attention_id'=>$attention_id
        ])->first();
        if (!empty($attention)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'不能重复关注！'
            ]);
        }
        $attention = new Attention();
        $attention->user_id = $uid;
        $attention->attention_id = $attention_id;
        if ($attention->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delAttention()
    {
        $uid = getUserToken(Input::get('token'));
        $attention_id = Input::get('attention_id');
        $attention = Attention::where([
            'user_id'=>$uid,
            'attention_id'=>$attention_id
        ])->first();
        if (empty($attention)){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
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
            $count = Commodity::where([
                'user_id'=>$attentions[$i]->attention_id,
                'enable'=>1,
                'pass'=>1
            ])->count();
            $attentions[$i]->commodity_count = $count;
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
        $uid = getUserToken(Input::get('token'));
        $attention = Attention::where([
            'attention_id'=>$id,
            'user_id'=>$uid
        ])->count();
        $user = User::find($id);
        if (empty($user)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'未找到该用户！'
            ]);
        }
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
        $count = Commodity::where([
            'user_id'=>$id,
            'enable'=>1,
            'pass'=>1
        ])->count();
        $commodities = Commodity::where([
            'user_id'=>$id,
            'enable'=>1,
            'pass'=>1
        ])->limit($limit)->offset(($page-1)*$limit)->get();
        if (count($commodities)>0){
            for ($i=0;$i<count($commodities);$i++){
                $commodities[$i]->type = CommodityType::find($commodities[$i]->type)->title;
                $commodities[$i]->picture = $commodities[$i]->pictures()->pluck('thumb_url')->first();
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'name'=>$user->name,
                'create_time'=>date('Y-m-d',strtotime($user->created_at)),
                'avatar'=>$user->avatar,
                'level'=>$level,
                'attention'=>$attention,
                'user_id'=>$id,
                'commodity_count'=>$count,
                'commodities'=>$commodities
            ]
        ]);
    }
    public function getBasicStore($id)
    {
        $user = User::find($id);
        if (empty($user)){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'未找到！'
            ]);
        }
        $member = Member::where('user_id','=',$id)->orderBy('id','DESC')->first();
        if (empty($member)){
            $level = 0;
        }else{
            if ($member->end_time<time()){
                $level = 0;
            }else{
                $level = $member->level;
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'avatar'=>$user->avatar,
                'level'=>$level,
                'name'=>$user->name
            ]
        ]);
    }
    public function addDescription()
    {
        $desc = new Description();
        $desc->type_id = Input::get('type_id');
        $desc->title = Input::get('title');
        if ($desc->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getDescriptions()
    {
        $type = Input::get('type');
        $descs = Description::where([
            'type_id'=>$type
        ])->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$descs
        ]);
    }
    public function changeState()
    {
        $uid = getUserToken(Input::get('token'));
        $commodity_id = Input::get('commodity_id');
        $commodity = Commodity::find($commodity_id);
        if ($commodity->user_id!=$uid){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'无权操作！'
            ]);
        }else{
            $enable = $commodity->enable;
            $commodity->enable = ($enable==0)?1:0;
            $commodity->save();
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }

    }
    public function formatCommodities($data,$lat,$lng)
    {
        if (empty($data)){
            return [];
        }
        for ($i=0;$i<count($data);$i++){
            $data[$i]->picture = $data[$i]->pictures()->pluck('thumb_url')->first();
            $dist = calculateDistance($data[$i]->latitude,$data[$i]->longitude,$lat,$lng);
            $data[$i]->dist = sprintf('%.2f',$dist);
        }
        return $data;
    }
}
