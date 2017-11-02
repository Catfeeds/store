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

Route::get('launcher/images','API\V1\LaunchImageController@getLaunchImages');
Route::get('enable/launcher/image/{id}','API\V1\LaunchImageController@enableLauncherImage');
Route::post('launcher/image','API\V1\LaunchImageController@addLaunchImage');
Route::post('store/type','API\V1\StoreController@addType');
Route::get('modify/store/type/{id}','API\V1\StoreController@modifyType');
Route::post('member/level','API\V1\SystemController@addMemberLevel');
Route::post('role','API\V1\SystemController@addRole');
Route::get('roles','API\V1\SystemController@getRoles');
Route::get('role/{id}','API\V1\SystemController@getRole');
Route::get('permissions','API\V1\SystemController@getPermissions');
Route::post('permission','API\V1\SystemController@addPermission');
Route::post('attach/permission','API\V1\SystemController@attachPermission');
Route::get('test',function (){
    $data = getAround(23.128788,113.284406,500);
    var_dump($data);
});
