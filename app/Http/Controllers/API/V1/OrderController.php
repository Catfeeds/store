<?php

namespace App\Http\Controllers\API\V1;

use App\Libraries\WxPay;
use App\Models\AmountOrder;
use App\Models\Commodity;
use App\Models\Member;
use App\Models\MemberLevel;
use App\Models\Order;
use App\Models\SysConfig;
use App\Models\UserAmount;
use App\Models\UserBuy;
use App\Models\WechatBind;
use App\PublishRecord;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Latrell\Alipay\Facades\AlipayMobile;
use Yansongda\Pay\Pay;

class OrderController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取单个用户的订单数据
     */
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 购买图片
     */
    public function buyCommodityPicture(Request $request)
    {
        $uid = getUserToken($request->get('token'));
        $commodity_id = $request->get('commodity_id');
        $type = $request->get('pay_type');
        $config = SysConfig::first();
        $number = self::makePaySn($uid);
        $title = Commodity::find($commodity_id)->title;
        $title = '即时查看-'.$title.'-图片';
        switch ($type){
            case 1:
                $bool = $this->scorePay($uid,$number,$title,$config->pic_score,2,$commodity_id);
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
                if ($this->makeOrder($uid,$number,$config->pic_price,$title,2,2,$commodity_id)){
                    $data = $this->aliPay($number,$title,$config->pic_price);
                }
                break;
            case 3:
                if ($this->makeOrder($uid,$number,$config->pic_price,$title,2,3,$commodity_id)){
                    $ip = $request->getClientIp();
                    $data = $this->wxPay($number,$title,$config->pic_price,$ip);
                }
                break;
        }
//        $buy = UserBuy::where([
//            'user_id'=>$uid,
//            'commodity_id'=>$commodity_id
//        ])->first();
//        $config = SysConfig::first();
//        if (empty($buy)){
//            $buy = new UserBuy();
//            $buy->user_id = $uid;
//            $buy->commodity_id = $commodity_id;
//            $buy->save();
//
//        }else{
//            if ($buy->pic ==0){
//                $number = self::makePaySn($uid);
//                $title = Commodity::find($commodity_id)->title;
//                $title = '即时查看-'.$title.'-图片';
//                switch ($type){
//                    case 1:
//                        $bool = $this->scorePay($uid,$number,$title,$config->pic_score,2,$buy->id);
//                        if ($bool){
//                            return response()->json([
//                                'return_code'=>"SUCCESS",
//                                'data'=>[]
//                            ]);
//                        }else{
//                            return response()->json([
//                                'return_code'=>"FAIL",
//                                'return_msg'=>'积分余额不足！'
//                            ]);
//                        }
//                        break;
//                    case 2:
//                        if ($this->makeOrder($uid,$number,$config->pic_price,$title,2,2,$commodity_id,$buy->id)){
//                            $data = $this->aliPay($number,$title,$config->pic_price);
//                        }
//                        break;
//                    case 3:
//                        if ($this->makeOrder($uid,$number,$config->pic_price,$title,3,3,$commodity_id,$buy->id)){
//                            $ip = $request->getClientIp();
//                            $data = $this->wxPay($number,$title,$config->pic_price,$ip);
//                        }
//                        break;
//                }
//            }
//
//        }
//        $data = empty($data)?[]:$data;
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 购买联系方式
     */
    public function buyCommodityPhone(Request $request)
    {
        $uid = getUserToken($request->get('token'));
        $commodity_id = $request->get('commodity_id');
        $type = $request->get('pay_type');
//        $buy = UserBuy::where([
//            'user_id'=>$uid,
//            'commodity_id'=>$commodity_id
//        ])->first();
        $config = SysConfig::first();
        $number = self::makePaySn($uid);
        $title = Commodity::find($commodity_id)->title;
        $title = '即时查看-'.$title.'-联系方式';
        switch ($type){
            case 1:
                $bool = $this->scorePay($uid,$number,$title,$config->phone_score,3,$commodity_id);
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
                if ($this->makeOrder($uid,$number,$config->phone_price,$title,3,2,$commodity_id)){
                    $data = $this->aliPay($number,$title,$config->phone_price);
                }
                break;
            case 3:
                if ($this->makeOrder($uid,$number,$config->phone_price,$title,3,3,$commodity_id)){
                    $ip = $request->getClientIp();
                    $data = $this->wxPay($number,$title,$config->phone_price,$ip);
                }
                break;
        }
//        if (empty($buy)){
//            $buy = new UserBuy();
//            $buy->user_id = $uid;
//            $buy->commodity_id = $commodity_id;
//            $buy->save();
//
//        }else{
//            if ($buy->phone ==0){
//                $number = self::makePaySn($uid);
//                $title = Commodity::find($commodity_id)->title;
//                $title = '即时查看-'.$title.'-联系方式';
//                switch ($type){
//                    case 1:
//                        $bool = $this->scorePay($uid,$number,$title,$config->phone_score,3,$buy->id);
//                        if ($bool){
//                            return response()->json([
//                                'return_code'=>"SUCCESS",
//                                'data'=>[]
//                            ]);
//                        }else{
//                            return response()->json([
//                                'return_code'=>"FAIL",
//                                'return_msg'=>'积分余额不足！'
//                            ]);
//                        }
//                        break;
//                    case 2:
//                        if ($this->makeOrder($uid,$number,$config->phone_price,$title,2,2,$buy->id)){
//                            $data = $this->aliPay($number,$title,$config->phone_price);
//                        }
//                        break;
//                    case 3:
//                        if ($this->makeOrder($uid,$number,$config->phone_price,$title,3,3,$buy->id)){
//                            $ip = $request->getClientIp();
//                            $data = $this->wxPay($number,$title,$config->phone_price,$ip);
//                        }
//                        break;
//                }
//            }
//
//        }
//        $data = empty($data)?[]:$data;
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }

    /**
     * @param $uid
     * @param $number
     * @param $price
     * @param $title
     * @param $type
     * @param $pay_type
     * @param $content
     * @param int $state
     * @return bool
     * 创建订单
     */
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

    /**
     * @param $number
     * @param $title
     * @param $price
     * @param $ip
     * @return mixed
     * 微信支付
     */
    public function wxPay($number,$title,$price,$ip)
    {
        $config = config('wxxcx');
        $pay = new Pay($config);
        $config_biz = [
            'out_trade_no' => $number,           // 订单号
            'total_fee' => intval($price*100),              // 订单金额，**单位：分**
            'body' => $title,                   // 订单描述
            'spbill_create_ip' => $ip,       // 支付人的 IP
        ];
        return $pay->driver('wechat')->gateway('app')->pay($config_biz);
    }

    /**
     * @param $number
     * @param $title
     * @param $price
     * @return mixed
     * 支付宝支付
     */
    public function aliPay($number,$title,$price)
    {
        $alipay = app('alipay.mobile');
        $alipay->setOutTradeNo($number);
        $alipay->setTotalFee($price);
        $alipay->setSubject($title);
        $alipay->setBody($title);

        // 返回签名后的支付参数给支付宝移动端的SDK。
        return $alipay->getPayPara();
//        $config = config('alipay');
//        $config_biz = [
//            'out_trade_no' => $number,                 // 订单号
//            'total_amount' => $price,                 // 订单金额，单位：元
//            'subject' => $title,   // 订单商品标题
//        ];
//        $pay = new Pay($config);
//        return $pay->driver('alipay')->gateway('app')->pay($config_biz);
    }

    /**
     * @param $uid
     * @param $number
     * @param $title
     * @param $price
     * @param $type
     * @param $content
     * @return bool
     * 积分支付
     */
    public function scorePay($uid,$number,$title,$price,$type,$content)
    {
        $user = User::find($uid);
        if ($user->score<$price){
            return false;
        }else{
            if ($this->makeOrder($uid,$number,$price,$title,$type,1,$content,1)){
                $user->score -= $price;
                $user->save();
                return true;
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 升级会员
     */
    public function addMember(Request $request)
    {
        $type = $request->get('pay_type');
        $uid = getUserToken($request->get('token'));
        $level_id = $request->get('level');
        $level = MemberLevel::where('level','=',$level_id)->first();
        $number = self::makePaySn($uid);
        switch ($type){
            case 2:
                if ($this->makeOrder($uid,$number,$level->price,'升级'.$level->level.'星会员',1,2,$level->level)){
                    $data = $this->aliPay($number,'升级'.$level->level.'星会员',$level->price);
                }
                break;
            case 3:
                $ip = $request->getClientIp();
                if ($this->makeOrder($uid,$number,$level->price,'升级'.$level->level.'星会员',1,3,$level->level)){
                    $data = $this->wxPay($number,'升级'.$level->level.'星会员',$level->price,$ip);
                }
        }


        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
    public function pay_notify(Request $request)
    {
        $config = config('wxxcx');
        $pay = new Pay($config);
        $verify = $pay->driver('wechat')->gateway('app')->verify($request->getContent());

        if ($verify) {
            $order = Order::where(['number'=>$verify['out_trade_no']])->first();
            if ($order->state==0){
                switch ($order->type){
                    case 1:
                        $level = MemberLevel::where('level','=',$order->content)->first();
                        $member = Member::where('user_id','=',$order->user_id)->first();
                        if (empty($member)){
                            $member = new Member();
                            $member->level = $level->level;
                            $member->end_time = intval(time()+$level->time);
                            $member->send_max = $level->send_max;
                            $member->send_daily = $level->send_daily;
                            $member->user_id = $order->user_id;
                            PublishRecord::where('user_id','=',$order->user_id)->delete();
                        }else{
                            $member->level = $level->level;
                            $member->end_time = intval(time()+$level->time);
                            $member->send_max = $level->send_max;
                            $member->send_daily = $level->send_daily;
                            PublishRecord::where('user_id','=',$order->user_id)->delete();
                        }
                        $member->save();
                        $order->state = 1;
                        break;
                    case 2:
//                        $buy = UserBuy::find($order->content);
//                        $buy->pic = 1;
//                        $buy->save();
                        $order->state = 1;
                        break;
                    case 3:
//                        $buy = UserBuy::find($order->content);
//                        $buy->phone = 1;
//                        $buy->save();
                        $order->state = 1;
                        break;
                    case 4:
                        $userAmount = UserAmount::where('user_id','=',$order->user_id)->first();
                        if (empty($userAmount)){
                            $userAmount = new UserAmount();
                            $userAmount->user_id = $order->user_id;
                            $userAmount->amount = 0;
                        }
                        $userAmount->amount += $order->price;
                        $userAmount->save();
                        break;
                }
                if ($order->save()){
                    return 'SUCCESS';
                }
//            file_put_contents('notify.txt', "收到来自微信的异步通知\r\n", FILE_APPEND);
//            file_put_contents('notify.txt', '订单号：' . $verify['out_trade_no'] . "\r\n", FILE_APPEND);
    //            file_put_contents('notify.txt', '订单金额：' . $verify['total_fee'] . "\r\n\r\n", FILE_APPEND);
            } else {
    //            file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
            }

             echo "success";
        }
    }
    public function getAllOrders()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $orderDb = Order::where('state','!=',0);
        //->where('pay_type','!=',1)
        $sum = $orderDb->sum('price');
        $pay_type = Input::get('pay_type');
        $start = Input::get('start');
        $end = Input::get('end');
        $level = Input::get('level');
        $uid = Input::get('user_id');
        if ($uid){
            $orderDb->where('user_id','=',$uid);
            $sum = $orderDb->sum('price');
        }
        if ($pay_type){
            $orderDb->where('pay_type','=',$pay_type);
            $sum = $orderDb->sum('price');
        }
        if ($level){
            $user_id = Member::whereIn('level',$level)->pluck('user_id');
            $orderDb->whereIn('user_id',$user_id);
            $sum = $orderDb->sum('price');
        }
        if ($start){
            $orderDb->whereBetween('created_at',[$start,$end]);
            $sum = $orderDb->sum('price');
        }
        $count = $orderDb->count();

        $data = $orderDb->limit($limit)->offset(($page-1)*$limit)->orderBy('id','DESC')->get();
        if (!empty($data)){
            for ($i=0;$i<count($data);$i++){
                $user = User::find($data[$i]->user_id);
                $data[$i]->username = empty($user)?'':$user->username;
                $data[$i]->member = Member::where('user_id','=',$data[$i]->user_id)->first();
            }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
            'sum'=>$sum,
            'data'=>$data
        ]);
    }
    public function alipayNotify(Request $request)
    {
        // 验证请求。
        $config = config('alipay');
        $pay = new Pay($config);

        if ($pay->driver('alipay')->gateway('app')->verify($request->all())) {
            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
            switch (Input::get('trade_status')) {
                case 'TRADE_SUCCESS':
                case 'TRADE_FINISHED':
                    // TODO: 支付成功，取得订单号进行其它相关操作。
//                Log::debug('Alipay notify get data verification success.', [
//                    'out_trade_no' => Input::get('out_trade_no'),
//                    'trade_no' => Input::get('trade_no')
//                ]);
                    $order = Order::where(['number'=>Input::get('out_trade_no')])->first();
                    if ($order->state==0){
                        switch ($order->type){
                            case 1:
                                $level = MemberLevel::where('level','=',$order->content)->first();
                                $member = Member::where('user_id','=',$order->user_id)->first();
                                if (empty($member)){
                                    $member = new Member();
                                    $member->level = $level->level;
                                    $member->end_time = intval(time()+$level->time);
                                    $member->send_max = $level->send_max;
                                    $member->send_daily = $level->send_daily;
                                    $member->user_id = $order->user_id;
                                    PublishRecord::where('user_id','=',$order->user_id)->delete();
                                }else{
                                    $member->level = $level->level;
                                    $member->end_time = intval(time()+$level->time);
                                    $member->send_max = $level->send_max;
                                    $member->send_daily = $level->send_daily;
                                    PublishRecord::where('user_id','=',$order->user_id)->delete();
                                }
                                $member->save();
                                $order->state = 1;
                                break;
                            case 2:
//                        $buy = UserBuy::find($order->content);
//                        $buy->pic = 1;
//                        $buy->save();
                                $order->state = 1;
                                break;
                            case 3:
//                        $buy = UserBuy::find($order->content);
//                        $buy->phone = 1;
//                        $buy->save();
                                $order->state = 1;
                                break;
                            case 4:
                                $userAmount = UserAmount::where('user_id','=',$order->user_id)->first();
                                if (empty($userAmount)){
                                    $userAmount = new UserAmount();
                                    $userAmount->user_id = $order->user_id;
                                    $userAmount->amount = 0;
                                }
                                $userAmount->amount += $order->price;
                                $userAmount->save();
                                $order->state = 1;
                                break;
                        }
                        if ($order->save()){
                            return 'SUCCESS';
                        }
//            file_put_contents('notify.txt', "收到来自微信的异步通知\r\n", FILE_APPEND);
//            file_put_contents('notify.txt', '订单号：' . $verify['out_trade_no'] . "\r\n", FILE_APPEND);
                        //            file_put_contents('notify.txt', '订单金额：' . $verify['total_fee'] . "\r\n\r\n", FILE_APPEND);
                    } else {
                        //            file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
                    }
                    break;
            }


            file_put_contents(storage_path('notify.txt'), "收到来自支付宝的异步通知\r\n", FILE_APPEND);
            file_put_contents(storage_path('notify.txt'), '订单号：' . $request->out_trade_no . "\r\n", FILE_APPEND);
            file_put_contents(storage_path('notify.txt'), '订单金额：' . $request->total_amount . "\r\n\r\n", FILE_APPEND);
        } else {
            file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
        }

        echo "success";
//        $handle  = fopen('alipay.txt','a+');
//        fwrite($handle,var_export($request->getContent(),true));
//        fclose($handle);
//
//        if (! app('alipay.mobile')->verify()) {
////            Log::notice('Alipay notify post data verification fail.', [
////                'data' => Request::instance()->getContent()
////            ]);
//            $handle  = fopen('alipay2.txt','a+');
//            fwrite($handle,var_export($request->getContent(),true));
//            fclose($handle);
//            return 'fail';
//        }
//
//        // 判断通知类型。
//
//
//
//        return 'success';
    }
    public function addUserAmount(Request $post)
    {
        $amount = $post->amount;
        $uid = getUserToken($post->token);
        $type = $post->pay_type;
        $number = self::makePaySn($uid);
        switch ($type){
            case 2:
                if ($this->makeOrder($uid,$number,$amount,'充值余额',4,2,$amount)){
                    $data = $this->aliPay($number,'充值余额',$amount);
                }
                break;
            case 3:
                $ip = $post->getClientIp();
                if ($this->makeOrder($uid,$number,$amount,'充值余额',4,3,$amount)){
                    $data = $this->wxPay($number,'充值余额',$amount,$ip);
                }
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$data
        ]);
    }
    public function withdrawAmount()
    {
        $token = Input::get('token');
        $amount = Input::get('amount');
        $user_id = getUserToken($token);
        $bind = WechatBind::where('user_id','=',$user_id)->first();
        if (empty($bind)){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'未绑定微信！'
            ]);
        }
        $userAmount = UserAmount::where('user_id','=',$user_id)->first();
        if (empty($userAmount)||$userAmount->amount<$amount){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'余额不足！'
            ]);
        }
        $order = [
            'partner_trade_no' => self::makePaySn($user_id),              //商户订单号
            'openid' => $bind->open_id,                        //收款人的openid
            'check_name' => 'NO_CHECK',            //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
            // 're_user_name'=>'张三',              //check_name为 FORCE_CHECK 校验实名的时候必须提交
            'amount' => $amount*100,                       //企业付款金额，单位为分
            'desc' => '帐户提现',                  //付款说明
        ];
        $config = config('wxxcx');
        $pay = new Pay($config);
        $result =  $pay->driver('wechat')->gateway('app')->transfer($order);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$result
        ]);
    }
}

