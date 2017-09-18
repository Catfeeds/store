<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\RegisterPost;
use App\Http\Requests\ResetPasswordPost;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Symfony\Component\CssSelector\Parser\Token;

class UserController extends Controller
{
    //
    public function register(RegisterPost $request)
    {
        $user = new User();
        $user->username = $request->get('username');
        $user->password = bcrypt($request->get('password'));
        $user->phone = $request->get('phone');
        $code = $request->get('code');
        if($user->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function login()
    {
        $username = Input::get('username');
        $password = Input::get('password');
        if (Auth::attempt(['username'=>$username,'password'=>$password],true)){
            $key = createNonceStr();
            setUserToken($key,Auth::id());
            return response()->json([
                'return_code'=>"SUCCESS",
                'data'=>$key
            ]);
        }
    }
    public function resetPassword(ResetPasswordPost $request)
    {

    }
    public function changePassword()
    {

    }
    public function sendCode()
    {

    }
    public function myPublish()
    {

    }
    public function delPublish()
    {

    }
    public function addReport()
    {

    }
    public function addReason()
    {

    }
    public function getToken()
    {
        $token = Input::get('token');
        $uid = getUserToken($token);
        dd($uid);
    }

}
