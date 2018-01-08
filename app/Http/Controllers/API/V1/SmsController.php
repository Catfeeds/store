<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\SendPost;
use App\Libraries\AliSms;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class SmsController extends Controller
{
    //
    public function send(SendPost $sendPost)
    {
        $number = $sendPost->get('number');
        $type = $sendPost->get('type');
        $code = getRandCode();
        $smsContent = [
            'code'=>$code
        ];
        switch ($type) {
            case 1:
                $data = [
                    'type'=>'register',
                    'code'=>$code
                ];
                break;
            case 2:
                $data = [
                    'type'=>'findPassword',
                    'code'=>$code
                ];
                break;
            case 3:
                $data = [
                    'type'=>'resetPassword',
                    'code'=>$code
                ];
                break;
            case 4:
                $data = [
                    'type'=>'apply',
                    'code'=>$code
                ];
                break;
            case 5:
                $data = [
                    'type'=>'modifyPhone',
                    'code'=>$code
                ];
                break;
            case 6:
                $data = [
                    'type'=>'login',
                    'code'=>$code
                ];
                break;

        }
        $data = AliSms::sendSms($number,config('alisms.VerificationCode'),$smsContent);
        if ($data->Code =='OK') {
            $data = serialize($data);
            setCode($number,$data);
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }else{
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'短信发送失败!'
            ]);
        }

    }
    public function sendModifySMS()
    {
        $user_id = getUserToken(Input::get('token'));
        $user = User::find($user_id);
        $code = getRandCode();
        $smsContent = [
            'code'=>$code
        ];
        $data = AliSms::sendSms($user->phone,config('alisms.VerificationCode'),$smsContent);
        if ($data->Code =='OK') {
            return response()->json([
                'return_code'=>'SUCCESS',
                'data'=>[
                    'code'=>$code
                ]
            ]);
        }
    }
}
