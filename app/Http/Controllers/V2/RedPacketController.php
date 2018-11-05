<?php

namespace App\Http\Controllers\V2;

use App\Models\Commodity;
use App\Models\CommodityRedpack;
use App\Models\RedpacketConfig;
use App\Models\UserAmount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class RedPacketController extends Controller
{
    //
    public function addConfig(Request $post)
    {
        $config = RedpacketConfig::first();
        if (empty($config)){
            $config = new RedpacketConfig();
        }
        $config->state = $post->state;
        $config->cash_ratio = $post->cash_ratio;
        $config->cash_max_day = $post->cash_max_day;
        $config->cash_min_day = $post->cash_min_day;
        $config->cash_price_max = $post->cash_price_max;
        $config->cash_price_min = $post->cash_price_min;
        $config->cash_total_max = $post->cash_total_max;
        $config->cash_total_min = $post->cash_total_min;
        $config->cash_number_max = $post->cash_number_max;
        $config->cash_number_min = $post->cash_number_min;
        $config->coupon_ratio = $post->coupon_ratio;
        $config->coupon_max_day = $post->coupon_max_day;
        $config->coupon_min_day = $post->coupon_min_day;
        $config->coupon_price_max = $post->coupon_price_max;
        $config->coupon_price_min = $post->coupon_price_min;
        $config->coupon_total_max = $post->coupon_total_max;
        $config->coupon_total_min = $post->coupon_total_min;
        $config->coupon_number_max = $post->coupon_number_max;
        $config->coupon_number_min = $post->coupon_number_min;
        if ($config->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getConfig()
    {
        $config = RedpacketConfig::first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$config
        ]);
    }
    public function addCommodityRedPacket(Request $post)
    {
        $config = RedpacketConfig::first();
        $commodity = Commodity::find($post->commodity_id);
        $userAmount = UserAmount::where('user_id','=',$commodity->user_id)->first();
        if (empty($userAmount)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'账户余额不足!'
            ]);
        }
//        $price = 0;
        $id = $post->id;
        if($id){
            $redpacket = CommodityRedpack::find($id);
        }else{
            $redpacket = CommodityRedpack::where('commodity_id','=',$post->commodity_id)->first();
            if (empty($redpacket)){
                $redpacket = new CommodityRedpack();
                $redpacket->commodity_id = $post->commodity_id;
            }
        }
        $redpacket->icon = $post->icon?$post->icon:'';
        $redpacket->start = strtotime($post->start);
        $redpacket->end = strtotime($post->end);
        $redpacket->cash_number = $post->number;
        $redpacket->distance = $post->distance;
        $redpacket->cash_all = $post->cash_all;
        $redpacket->cash_min = $post->cash_min;
        $redpacket->cash_max = $post->cash_max;
        $redpacket->title = $post->title;
        $redpacket->coupon_all = $post->coupon_all?$post->coupon_all:0;
        $redpacket->coupon_min = $post->coupon_min?$post->coupon_min:0;
        $redpacket->coupon_max = $post->coupon_max?$post->coupon_max:0;
        $redpacket->coupon_end = $post->coupon_end?$post->coupon_end:0;
        $redpacket->coupon_number = $post->number?$post->number:0;
//        $redpacket->coupon_max = $post->coupon_max?$post->coupon_max:0;
        $redpacket->code = $post->code?$post->code:'';
        $redpacket->coupon_title = $post->coupon_title?$post->coupon_title:'';
        $price = $redpacket->cash_all+$redpacket->cash_all*($config->cash_ratio/100)+$redpacket->coupon_all*($config->coupon_ratio/100);
        if ($price>$userAmount->amount){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'账户余额不足!'
            ]);
        }
        if ($redpacket->save()){
            $userAmount->amount -= $price;
            $userAmount->save();
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
        return response()->json([
            'return_code'=>"FAIL",
            'return_msg'=>'系统错误!'
        ]);
    }
    public function getCommodityRedPacket()
    {
        $commodity_id = Input::get('commodity_id');
        $config = CommodityRedpack::where('commodity_id','=',$commodity_id)->first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$config
        ]);
    }
}
