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
            $title = '即时查看-'.$title.'-图片';
            switch ($type){
                case 1:
                    $this->scorePay($uid,$number,$title,3,2,$buy->id);
                    break;
                case 3:
                    if ($this->makeOrder($uid,$number,0.3*100,$title,2,3)){
                        $data = $this->wxPay($number,$title,0.3*100);
                    }
            }
        }else{
            $data =[];

        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
    public function buyCommodityPhone(Request $request)
    {
        $uid = getUserToken($request->get('token'));
        $commodity_id = $request->get('commodity_id');
        $type = $request->get('pay_type');
        $buy = UserBuy::where([
            'user_id'=>$uid,
            'commodity_id'=>$commodity_id
        ])->first();
        if (empty($buy)||$buy->phone ==0){
            $buy = new UserBuy();
            $buy->user_id = $uid;
            $buy->commodity_id = $commodity_id;
            $buy->save();
            $number = self::makePaySn($uid);
            $title = Commodity::find($commodity_id)->title;
            $title = '即时查看-'.$title.'-联系方式';
            switch ($type){
                case 1:
                    $this->scorePay($uid,$number,$title,3,3,$buy->id);
                    break;
                case 3:
                    if ($this->makeOrder($uid,$number,0.3*100,$title,3,3,$buy->id)){
                        $data = $this->wxPay($number,$title,0.3*100);
                    }
            }
        }else{
            $data =[];

        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
    public function makeOrder($uid,$number,$price,$title,$type,$pay_type,$content,$state=0)
    {
        $order = new Order();
        $order->user_id = $uid;
        $order->price = $price;
        $order->number = $number;
        $order->title = $title;
        $order->type = $type;
        $order->pay_type = $pay_type;
        $order->content = $content;
        $order->state = $state;
        if ($order->save()){
            return true;
        }
        return false;
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
    public function scorePay($uid,$number,$title,$price,$type,$content)
    {
        $user = User::find($uid);
        if ($user->score<$price){
            return response()->json([
                'return_code'=>"FAIL",
                'return_msg'=>'积分余额不足！'
            ]);
        }else{
            if ($this->makeOrder($uid,$number,$price,$title,$type,1,$content,1)){
                $user->score -= $price;
                $user->save();
                $buy = UserBuy::find($content);
                switch ($type){
                    case 2:
                        $buy->pic = 1;
                        $buy->save();
                        break;
                    case 3:
                        $buy->phone = 1;
                        $buy->save();
                        break;
                }
                return response()->json([
                    'return_code'=>"SUCCESS",
                    'data'=>[]
                ]);
            }
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
    public function pay_notify(Request $request)
    {
        $data = $request->getContent();
        $wx = WxPay::xmlToArray($data);
        $wspay = new WxPay(config('wxxcx.app_id'),config('wxxcx.mch_id'),config('wxxcx.api_key'),$wx['openid']);
        $data = [
            'appid'=>$wx['appid'],
            'attach'=>$wx['attach'],
            'bank_type'=>$wx['bank_type'],
            'fee_type'=>$wx['fee_type'],
            'is_subscribe'=>$wx['is_subscribe'],
            'mch_id'=>$wx['mch_id'],
            'nonce_str'=>$wx['nonce_str'],
            'openid'=>$wx['openid'],
            'out_trade_no'=>$wx['out_trade_no'],
            'result_code'=>$wx['result_code'],
            'return_code'=>$wx['return_code'],
            'sub_mch_id'=>$wx['sub_mch_id'],
            'time_end'=>$wx['time_end'],
            'total_fee'=>$wx['total_fee'],
            'trade_type'=>$wx['trade_type'],
            'transaction_id'=>$wx['transaction_id']
        ];
        $sign = $wspay->getSign($data);
        if ($sign == $wx['sign']){
            $order = Order::where(['number'=>$wx['out_trade_no']])->first();
            if ($order->state==0){
                switch ($order->type){
                    case 1:
                        $lever = MemberLevel::find($order->content);

                        break;
                    case 2:
                        $buy = UserBuy::find($order->content);
                        $buy->pic = 1;
                        $buy->save();
                        $order->state = 1;
                        break;
                    case 3:
                        $buy = UserBuy::find($order->content);
                        $buy->phone = 1;
                        $buy->save();
                        $order->state = 1;
                        break;
                    case 4:
                        break;
                }
                if ($order->save()){
                    return 'SUCCESS';
                }
            }else{
                return 'SUCCESS';
            }

        }
        return 'ERROR';
    }
}
