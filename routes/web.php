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

Route::group(['middleware'=>'cross'],function (){
    Route::post('login','API\V1\UserController@adminLogin');
    Route::get('test',function (){
       dd(\Illuminate\Support\Facades\Auth::user());
    });
    Route::group(['middleware'=>'auth'],function (){
        Route::get('launcher/images','API\V1\LaunchImageController@getLaunchImages');
        Route::get('enable/launcher/image/{id}','API\V1\LaunchImageController@enableLauncherImage');
        Route::post('launcher/image','API\V1\LaunchImageController@addLaunchImage');
        Route::post('store/type','API\V1\StoreController@addType');
        Route::get('modify/store/type/{id}','API\V1\StoreController@modifyType');
        Route::post('member/level','API\V1\SystemController@addMemberLevel');
        Route::get('member/levels','API\V1\SystemController@getMemberLevels');
        Route::get('member/level/{id}','API\V1\SystemController@getMemberLevel');
        Route::get('del/member/level/{id}','API\V1\SystemController@delMemberLevel');
        Route::post('role','API\V1\SystemController@addRole');
        Route::get('roles','API\V1\SystemController@getRoles');
        Route::get('role/{id}','API\V1\SystemController@getRole');
        Route::get('permissions','API\V1\SystemController@getPermissions');
        Route::post('permission','API\V1\SystemController@addPermission');
        Route::post('attach/permission','API\V1\SystemController@attachPermission');
    });
});
