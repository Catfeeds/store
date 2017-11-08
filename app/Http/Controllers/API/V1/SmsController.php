<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\SendPost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        }
        if (sendSMS($number,config('alisms.VerificationCode'),$smsContent)) {
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
}
