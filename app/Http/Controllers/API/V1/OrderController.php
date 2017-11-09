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
use Illuminate\Support\Facades\Input;
use Latrell\Alipay\Facades\AlipayMobile;
use Yansongda\Pay\Pay;

class OrderController extends Controller
{
    //


    public function getOrders()
    {
        $uid = getUserToken(Input::get('token'));
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $orders = Order::where([
            'state'=>'1',
            'user_id'=>$uid
        ])->limit($limit)->offset(($page-1)*$limit)->orderBy('id','DESC')->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$orders
        ]);
    }

    public function buyCommodityPicture(Request $request)
    {
        $uid = getUserToken($request->get('token'));
        $commodity_id = $request->get('commodity_id');
        $type = $request->get('pay_type');
        $buy = UserBuy::where([
            'user_id'=>$uid,
            'commodity_id'=>$commodity_id
        ])->first();
        if (empty($buy)){
            $buy = new UserBuy();
            $buy->user_id = $uid;
            $buy->commodity_id = $commodity_id;
            $buy->save();
            $number = self::makePaySn($uid);
            $title = Commodity::find($commodity_id)->title;
            $title = '即时查看-'.$title.'-图片';
            switch ($type){
                case 1:
                    $bool = $this->scorePay($uid,$number,$title,3,2,$buy->id);
                    if ($bool){
                        return response()->json([
                            'return_code'=>"SUCCESS",
                            'data'=>[]
                        ]);
                    }else{
                        return response()->json([
                            'return_code'=>"FAIL",
                            'return_msg'=>'积分余额不足！'
                        ]);
                    }
                    break;
                case 2:
                    if ($this->makeOrder($uid,$number,0.3,$title,2,2,$commodity_id)){
                        $data = $this->aliPay($number,$title,0.3);
                    }
                    break;
                case 3:
                    if ($this->makeOrder($uid,$number,0.3,$title,2,3,$commodity_id)){
                        $ip = $request->getClientIp();
                        $data = $this->wxPay($number,$title,0.3*100,$ip);
                    }
                    break;
            }
        }else{
            if ($buy->pic ==0){
                $number = self::makePaySn($uid);
                $title = Commodity::find($commodity_id)->title;
                $title = '即时查看-'.$title.'-图片';
                switch ($type){
                    case 1:
                        $bool = $this->scorePay($uid,$number,$title,3,2,$buy->id);
                        if ($bool){
                            return response()->json([
                                'return_code'=>"SUCCESS",
                                'data'=>[]
                            ]);
                        }else{
                            return response()->json([
                                'return_code'=>"FAIL",
                                'return_msg'=>'积分余额不足！'
                            ]);
                        }
                        break;
                    case 2:
                        if ($this->makeOrder($uid,$number,0.3,$title,2,2,$commodity_id,$buy->id)){
                            $data = $this->aliPay($number,$title,0.3);
                        }
                        break;
                    case 3:
                        if ($this->makeOrder($uid,$number,0.3,$title,3,3,$commodity_id,$buy->id)){
                            $ip = $request->getClientIp();
                            $data = $this->wxPay($number,$title,0.3*100,$ip);
                        }
                        break;
                }
            }

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
        if (empty($buy)){
            $buy = new UserBuy();
            $buy->user_id = $uid;
            $buy->commodity_id = $commodity_id;
            $buy->save();
            $number = self::makePaySn($uid);
            $title = Commodity::find($commodity_id)->title;
            $title = '即时查看-'.$title.'-联系方式';
            switch ($type){
                case 1:
                    $bool = $this->scorePay($uid,$number,$title,3,3,$buy->id);
                    if ($bool){
                        return response()->json([
                            'return_code'=>"SUCCESS",
                            'data'=>[]
                        ]);
                    }else{
                        return response()->json([
                            'return_code'=>"FAIL",
                            'return_msg'=>'积分余额不足！'
                        ]);
                    }
                    break;
                case 2:
                    if ($this->makeOrder($uid,$number,0.3,$title,2,2,$buy->id)){
                        $data = $this->aliPay($number,$title,0.3);
                    }
                    break;
                case 3:
                    if ($this->makeOrder($uid,$number,0.3,$title,3,3,$buy->id)){
                        $ip = $request->getClientIp();
                        $data = $this->wxPay($number,$title,0.3*100,$ip);
                    }
                    break;
            }
        }else{
            if ($buy->phone ==0){
                $number = self::makePaySn($uid);
                $title = Commodity::find($commodity_id)->title;
                $title = '即时查看-'.$title.'-联系方式';
                switch ($type){
                    case 1:
                        $bool = $this->scorePay($uid,$number,$title,3,3,$buy->id);
                        if ($bool){
                            return response()->json([
                                'return_code'=>"SUCCESS",
                                'data'=>[]
                            ]);
                        }else{
                            return response()->json([
                                'return_code'=>"FAIL",
                                'return_msg'=>'积分余额不足！'
                            ]);
                        }
                        break;
                    case 2:
                        if ($this->makeOrder($uid,$number,0.3,$title,2,2,$buy->id)){
                            $data = $this->aliPay($number,$title,0.3);
                        }
                        break;
                    case 3:
                        if ($this->makeOrder($uid,$number,0.3,$title,3,3,$buy->id)){
                            $ip = $request->getClientIp();
                            $data = $this->wxPay($number,$title,0.3*100,$ip);
                        }
                        break;
                }
            }

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
        $config = config('wxxcx');
        $pay = new Pay($config);
        $config_biz = [
            'out_trade_no' => $number,           // 订单号
            'total_fee' => $price,              // 订单金额，**单位：分**
            'body' => $title,                   // 订单描述
            'spbill_create_ip' => $ip,       // 支付人的 IP
        ];
        return $pay->driver('wechat')->gateway('app')->pay($config_biz);
    }
    public function aliPay($number,$title,$price)
    {
        $alipay = app('alipay.mobile');
        $alipay->setOutTradeNo($number);
        $alipay->setTotalFee($price);
        $alipay->setSubject($title);
        $alipay->setBody($title);

        // 返回签名后的支付参数给支付宝移动端的SDK。
        return $alipay->getPayPara();
    }
    public function scorePay($uid,$number,$title,$price,$type,$content)
    {
        $user = User::find($uid);
        if ($user->score<$price){
            return false;
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
                return true;
            }
        }
    }
    public function addMember(Request $request)
    {
        $type = $request->get('pay_type');
        $uid = getUserToken($request->get('token'));
        $level_id = $request->get('level');
        $level = MemberLevel::find($level_id);
        $number = self::makePaySn($uid);
        switch ($type){
            case 2:
                if ($this->makeOrder($uid,$number,$level->price,'升级会员',3,2,$level->level)){
                    $data = $this->aliPay($number,'升级会员',$level->price);
                }
                break;
            case 3:
                $ip = $request->getClientIp();
                if ($this->makeOrder($uid,$number,$level->price,'升级会员',3,3,$level->level)){
                    $data = $this->wxPay($number,'升级会员',$level->price*100,$ip);
                }
        }


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

