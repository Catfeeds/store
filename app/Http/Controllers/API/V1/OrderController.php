<?php

namespace App\Http\Controllers\API\V1;

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
        $type = $request->get('type');
        $buy = UserBuy::where([
            'user_id'=>$uid,
            'commodity_id'=>$commodity_id
        ])->first();
        if (empty($buy)){
            $buy = new UserBuy();
            $buy->user_id = $uid;
            $buy->commodity_id = $commodity_id;
            $buy->save();
        }

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
    public function wxPay()
    {

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
        $level = $request->get('level');
    }
}
