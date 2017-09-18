<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('json',function (){
    return response()->json([
        [
            'name'=>'注册',
            'url'=>url('/register/test')
        ],
        [
            'name'=>'登录',
            'url'=>url('/login')
        ],
        [
            'name'=>'重置密码',
            'url'=>url('/findmypsd')
        ],
        [
            'name'=>'修改密码',
            'url'=>url('/changepsd')
        ],
        [
            'name'=>'验证码',
            'url'=>url('/code')
        ],
        [
            'name'=>'类型',
            'url'=>url('/type')
        ],
        [
            'name'=>'消息列表',
            'url'=>url('stores')
        ],
        [
            'name'=>'签到',
            'url'=>url('sign')
        ],
        [
            'name'=>'签到列表',
            'url'=>url('signlist')
        ],
        [
            'name'=>'用户中心',
            'url'=>url('usercenter')
        ],
        [
            'name'=>'个人信息',
            'url'=>url('/userinfo')
        ],
        [
            'name'=>'账单列表',
            'url'=>url('order')
        ],
        [
            'name'=>'发布新消息',
            'url'=>url('/message')
        ],
        [
            'name'=>'举报',
            'url'=>url('/jubao')
        ],
        [
            'name'=>'我的发布',
            'url'=>url('/publish')
        ],
    ]);
});
Route::get('/register/test',function (){
    return response()->json([
        'name'=>'register',
        'path'=>'api/v1/register',
        'type'=>'POST',
        'param'=>[
            'username :must',
            'password :must',
            'code :must',
            'phone :must'
        ],
        'success'=>[
            'return_code'=>'SUCCESS'
        ],
        'fail'=>[
            'return_code'=>'FAIL',
            'return_msg'=>'FAIL MSG'
        ]
    ]);
});
Route::get('/login',function (){
    return response()->json([
        'name'=>'login',
        'path'=>'api/v1/login',
        'type'=>'post',
        'param'=>[
            'username/phone :must',
            'password :must'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>'$token'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
Route::get('/findmypsd',function (){
    return response()->json([
        'name'=>'ResetPassword',
        'path'=>'api/v1/password/reset',
        'type'=>'post',
        'param'=>[
            'username :must',
            'password :must',
            'code :must'
        ],
        'success'=>[
            'return_code'=>'success'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
Route::get('/changepsd',function (){
    return response()->json([
        'name'=>'ChangePassword',
        'path'=>'api/v1/password/change',
        'type'=>'post',
        'param'=>[
            'username :must',
            'origin_password :must',
            'password :must',
            'code :must'
        ],
        'success'=>[
            'return_code'=>'success'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
Route::get('/code',function (){
    return response()->json([
        'name'=>'SendVerificationCode',
        'path'=>'api/v1/code',
        'type'=>'post',
        'param'=>[
            'phone :must',
            'type :must value:register,reset,change,publish,report'
        ],
        'success'=>[
            'return_code'=>'success'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
Route::get('type',function (){
    return response()->json([
        'name'=>'Get Commodity Type',
        'path'=>'api/v1/type',
        'type'=>'get',
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                [
                    'id'=>'1',
                    'name'=>'name'
                ],
                [
                    'id'=>'2',
                    'name'=>'name'
                ],
                [
                    'id'=>'3',
                    'name'=>'name'
                ]
            ]
        ]
    ]);
});
Route::get('stores',function (){
    return response()->json([
        'name'=>'GetCommodities',
        'path'=>'api/v1/commodities',
        'type'=>'get',
        'param'=>[
            'type :options',
            'latitude :must',
            'longitude :must'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                [
                    'id'=>'1',
                    'latitude'=>'23.2132',
                    'longitude'=>'103.1231',
                    'mixed'
                ],
                [
                    'id'=>'2',
                    'latitude'=>'23.2132',
                    'longitude'=>'103.1231',
                    'mixed'
                ],
                [
                    'id'=>'3',
                    'latitude'=>'23.2132',
                    'longitude'=>'103.1231',
                    'mixed'
                ]
            ]
        ]
    ]);
});

Route::get('sign',function (){
    return response()->json([
        'name'=>'Sign',
        'path'=>"api/v1/sign",
        'type'=>'post',
        'param'=>[
            'token :must'
        ],
        'success'=>[
            'return_code'=>'success'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
Route::get('signlist',function (){
    return response()->json([
        'name'=>'GetSignList',
        'path'=>'api/v1/sign/list',
        'type'=>'get',
        'param'=>[
            'token'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                '1',
                '4',
                '7',
                '15',
                '29'
            ]
        ]
    ]);
});
Route::get('usercenter',function (){
    return response()->json([
        'name'=>'UserCenter',
        'path'=>'api/v1/user/data',
        'type'=>'get',
        'param'=>[
            'token :must'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                'score'=>'200',
                'publish'=>'20'
            ]
        ]
    ]);
});
Route::get('userinfo',function (){
    return response()->json([
        'name'=>'getUserInfo',
        'path'=>'api/v1/user/info',
        'type'=>'get',
        'param'=>[
            'token :must'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                'avatar'=>'http://host/1.jpg',
                'username'=>'username',
                'nickname'=>'nickname',
                'rank'=>'1',
                'phone'=>'1',
                'WeChat'=>'0',
                'QQ'=>'1'
            ]
        ]
    ]);
});
Route::get('order',function (){
    return response()->json([
        'name'=>'GetOrderList',
        'path'=>'api/v1/orders',
        'type'=>'get',
        'param'=>[
            'token :must',
            'page :optional default:1'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                [
                    'id'=>'1',
                    'title'=>'in',
                    'price'=>'30',
                    'time'=>'20-15-5',
                    'type'=>'1'
                ],
                [
                    'id'=>'2',
                    'title'=>'out',
                    'price'=>'20',
                    'time'=>'20-15-5',
                    'type'=>'2'
                ]
            ]
        ]
    ]);
});
Route::get('message',function (){
    return response()->json([
        'name'=>'PublishNewMoment',
        'path'=>'api/v1/commodity',
        'type'=>'post',
        'param'=>[
            'token :must',
            'title :must',
            'address :must',
            'price :must',
            'type :must',
            'base :must',
            'detail :optional',
            'code :must',
            'QQ :optional',
            'WeChat :optional',
            'pictures :must value [url1,url2]'
        ],
        'success'=>[
            'return_code'=>'success'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
route::get('jubao',function (){
    return response()->json([
        'name'=>'MessageReport',
        'path'=>'api/v1/report',
        'type'=>'post',
        'param'=>[
            'type :must',
            'remark :optional',
            'moment_id :must',
            'contact :must',
            'code :must',
            'token :must'
        ],
        'success'=>[
            'return_code'=>'success'
        ],
        'fail'=>[
            'return_code'=>'fail',
            'return_msg'=>'fail msg'
        ]
    ]);
});
Route::get('publish',function (){
    return response()->json([
        'name'=>'MyPublishList',
        'path'=>'api/v1/my/commodity',
        'type'=>'get',
        'param'=>[
            'token :must',
            'type :must',
            'page :optional default 1'
        ],
        'success'=>[
            'return_code'=>'success',
            'data'=>[
                [
                    'id'=>'23231',
                    'pic'=>'http://host/1.jpg',
                    'title'=>'title',
                    'address'=>'guangzhou',
                    'time'=>'2045-48-5',
                    'read'=>232,
                    'price'=>323.5,
                    'type'=>'type'
                ],
                [
                    'id'=>'232231',
                    'pic'=>'http://host/1.jpg',
                    'title'=>'title',
                    'address'=>'guangzhou',
                    'time'=>'2045-48-5',
                    'read'=>232,
                    'price'=>323.5,
                    'type'=>'type'
                ]
            ]
        ]
    ]);
});