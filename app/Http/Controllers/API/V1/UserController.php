<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\RegisterPost;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

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
    }
    public function resetPassword()
    {

    }
    public function changePassword()
    {

    }
    public function sendCode()
    {

    }

}
