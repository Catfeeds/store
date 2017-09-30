<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\CommodityPost;
use App\Http\Requests\FilterPost;
use App\Http\Requests\RejectPost;
use App\Http\Requests\ReportPost;
use App\Models\Commodity;
use App\Models\CommodityPicture;
use App\Models\CommodityType;
use App\Models\Reject;
use App\Models\Report;
use App\Models\TypeList;
use App\Models\UserBuy;
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
            ],404);
        }
        $needPay = \config('store.needPay');
        if ($needPay){
            $uid = getUserToken(Input::get('token'));
            if (!$uid){
                $commodity->phone = '***********';
            }else{
                $record = UserBuy::where('user_id','=',$uid)->where('commodity_id','=',$id)->first();
                if (empty($record)){
                    $commodity->phone = '***********';
                }else{
                    $commodity->phone = ($record->phone==1)?$commodity->phone:'***********';
                    if ($record->pic==1){
                        $commodity->pictures = $commodity->pictures()->pluck('thumb_url');
                    }
                }
            }

        }else{
            $commodity->pictures = $commodity->pictures()->pluck('thumb_url');
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$commodity
        ]);
    }

    public function getCommodities(FilterPost $filterPost)
    {
        $page = $filterPost->get('page',1);
        $limit = $filterPost->get('limit',10);
        $type = $filterPost->get('type');
        if (isset($type)){
            $category_id = TypeList::where('type_id','=',$type)->limit($limit)->offset(($page)-1)->pluck('commodity_id');
            $commodities = Commodity::whereIn('id',$category_id)->get();
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
                ],404);
            }else{
                if ($commodity->user_id !=$uid){
                    return response()->json([
                        'return_code'=>"FAIL",
                        'return_msg'=>'无权修改该信息!'
                    ],403);
                }
                $commodity->title = $commodityPost->get('title');
                $commodity->price = $commodityPost->get('price');
                $commodity->description = $commodityPost->get('description');
                $commodity->detail = $commodityPost->get('detail',null);
                $commodity->phone = $commodityPost->get('phone');
                $commodity->QQ = $commodityPost->get('qq',null);
                $commodity->WeChat = $commodityPost->get('wechat',null);
                $commodity->latitude = $commodityPost->get('latitude');
                $commodity->longitude = $commodityPost->get('longitude');
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
            $commodity->user_id = $uid;
        }
        if ($commodity->save()){
            $type = $commodityPost->get('type');
            if (!empty($type)){
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
            ],404);
        }
        if ($commodity->user_id !=$uid){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'无权修改该信息!'
            ],403);
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
        $reject->report_id = $rejectPost->get('report_id');
        $reject->detail = $rejectPost->get('detail');
        $reject->phone = $rejectPost->get('phone');
        if ($reject->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }


}
