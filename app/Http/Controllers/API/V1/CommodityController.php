<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\CommodityPost;
use App\Http\Requests\FilterPost;
use App\Http\Requests\ReportPost;
use App\Models\Commodity;
use App\Models\CommodityType;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

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
            $commodities = Commodity::where('category_id','=',$type)->limit($limit)->offset(($page)-1)->get();
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
        if (isset($id)){
            $commodity = Commodity::find($id);
            if (empty($commodity)){
                return response()->json([
                    'return_code'=>"FAIL",
                    'return_msg'=>'没找到该消息!'
                ]);
            }
        }else{
            $commodity = new Commodity();
        }
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);
    }

}
