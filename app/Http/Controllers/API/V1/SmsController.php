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
        $code = rand(1000,9999);
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
        }
        $data = serialize($data);
        setCode($number,$data);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>unserialize($data)
        ]);
    }
}
