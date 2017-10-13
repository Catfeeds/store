<?php

namespace App\Http\Controllers\API\V1;

use App\Libraries\WxPay;
use App\Models\Commodity;
use App\Models\MemberLevel;
use App\Models\Order;
use App\Models\UserBuy;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    //
    public function buyCommodityPicture(Request $request)
    {
        $uid = getUserToken($request->get('token'));
        $commodity_id = $request->get('commodity_id');
        $type = $request->get('pay_type');
        $buy = UserBuy::where([
            'user_id'=>$uid,
            'commodity_id'=>$commodity_id
        ])->first();
        if (empty($buy)||$buy->pic ==0){
            $buy = new UserBuy();
            $buy->user_id = $uid;
            $buy->commodity_id = $commodity_id;
            $buy->save();
            $number = self::makePaySn($uid);
            $title = Commodity::find($commodity_id)->title;
            switch ($type){
                case 1:
                    $data = $this->scorePay();
                    break;
                case 2:
                    $data = $this->wxPay($number,'即时查看-'.$title.'-图片',0.3*100);
                    $order = new Order();
                    $order->number = $number;
                    break;
            }
        }else{
            $data =[];

        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
    public function buyCommodityPhone()
    {
    }
    public function makeOrder()
    {

    }
    public function doPay()
    {

    }
    public function wxPay($number,$title,$price,$ip)
    {
        $payment = new WxPay(config('wxxcx.app_id'),config('wxxcx.mch_id'),config('wxxcx.api_key'));
        return $payment->pay($number,$title,$price,$ip);
    }
    public function aliPay()
    {

    }
    public function scorePay($uid,$bid)
    {
        $user = User::find($uid);
        if ($user->score<1){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'积分余额不足！'
            ]);
        }else{
            $order = new Order();
        }
    }
    public function addMember(Request $request)
    {
        $uid = getUserToken($request->get('token'));
        $level_id = $request->get('level');
        $pay_type = $request->get('pay_type');
        $level = MemberLevel::find($level_id);
        $order = new Order();
        $order->user_id = $uid;
        $order->number = self::makePaySn($uid);
        $order->title = '升级会员';
        $order->type = 2;
        $order->pay_type = $pay_type;
        $order->price = $level->price;
        $ip = $request->getClientIp();
        $data = $this->wxPay($order->number,$order->title,$order->price,$ip);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
}
