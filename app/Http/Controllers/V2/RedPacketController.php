<?php

namespace App\Http\Controllers\V2;

use App\Models\CommodityRedpack;
use App\Models\RedpacketConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $redpacket = CommodityRedpack::where('commodity_id','=',$post->commodity_id)->first();
        if (empty($redpacket)){
            $redpacket = new CommodityRedpack();
            $redpacket->commodity_id = $post->commodity_id;
        }
        $redpacket->icon = $post->icon;
        $redpacket->start = strtotime($post->start);
        $redpacket->end = strtotime($post->end);
        $redpacket->number = $post->number;
        $redpacket->distance = $post->distance;
        $redpacket->cash_all = $post->cash_all;
        $redpacket->cash_min = $post->cash_min;
        $redpacket->cash_max = $post->cash_max;
        $redpacket->title = $post->title;
        $redpacket->coupon_all = $post->coupon_all;
        $redpacket->coupon_min = $post->coupon_min;
        $redpacket->coupon_max = $post->coupon_max;
        $redpacket->coupon_end = $post->coupon_end;
        $redpacket->coupon_max = $post->coupon_max;
        $redpacket->code = $post->code;
        $redpacket->coupon_title = $post->coupon_title;
        if ($redpacket->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
//    public function get
}
