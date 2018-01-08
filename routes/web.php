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
//Route::get('article','API\V1\SystemController@getArticle');
Route::get('qrcode/{id}','API\V1\SystemController@showStore');
Route::get('activity/{code}','API\V1\SystemController@showShareActivity');
Route::get('test',function (){
    $data = \App\Libraries\AliSms::sendSms();
    dd($data);
});
Route::group(['middleware'=>'cross'],function (){
    Route::any('upload','API\V1\UploadController@uploadImage');
    Route::post('login','API\V1\UserController@adminLogin');
    Route::get('logout','API\V1\UserController@adminLogout');
    Route::group(['middleware'=>'auth'],function (){
        Route::get('launcher/images','API\V1\LaunchImageController@getLaunchImages')->middleware('permission:luancher');
        Route::get('enable/launcher/image/{id}','API\V1\LaunchImageController@enableLauncherImage')->middleware('permission:luancher');
        Route::post('launcher/image','API\V1\LaunchImageController@addLaunchImage')->middleware('permission:luancher');
        Route::get('del/image/{id}','API\V1\LaunchImageController@delLauncherImage')->middleware('permission:luancher');
        Route::post('store/type','API\V1\StoreController@addType')->middleware('permission:type');
        Route::get('modify/store/type/{id}','API\V1\StoreController@modifyType')->middleware('permission:type');
        Route::post('member/level','API\V1\SystemController@addMemberLevel')->middleware('permission:memberlevel');
        Route::get('member/levels','API\V1\SystemController@getMemberLevels')->middleware('permission:memberlevel');
        Route::get('member/level/{id}','API\V1\SystemController@getMemberLevel')->middleware('permission:memberlevel');
        Route::get('del/member/level/{id}','API\V1\SystemController@delMemberLevel')->middleware('permission:memberlevel');
        Route::post('role','API\V1\SystemController@addRole')->middleware('permission:rolelist');
        Route::get('roles','API\V1\SystemController@getRoles')->middleware('permission:rolelist');
        Route::get('role/{id}','API\V1\SystemController@getRole')->middleware('permission:rolelist');
        Route::get('del/role/{id}','API\V1\SystemController@delRole')->middleware('permission:rolelist');
        Route::get('permissions','API\V1\SystemController@getPermissions');
        Route::post('permission','API\V1\SystemController@addPermission');
        Route::post('attach/role','API\V1\SystemController@attachRole')->middleware('permission:attachRole');
        Route::post('type','API\V1\CommodityController@addCommodityType')->middleware('permission:type');
        Route::get('types','API\V1\CommodityController@getTypes')->middleware('permission:type');
        Route::get('modify/type/{id}','API\V1\CommodityController@delType')->middleware('permission:type');
        Route::post('qrcode/{id}','API\V1\SystemController@addQrCode')->middleware('permission:logo');
        Route::get('qrcode','API\V1\SystemController@getQrCode')->middleware('permission:logo');

        Route::post('advert','API\V1\SystemController@addAdvert')->middleware('permission:advert');
        Route::get('adverts','API\V1\SystemController@getAllAdverts')->middleware('permission:advert');
        Route::get('del/advert/{id}','API\V1\SystemController@delAdvert')->middleware('permission:advert');
        Route::get('cities','API\V1\SystemController@getCities');
        Route::post('article','API\V1\SystemController@addArticle')->middleware('permission:article');
        Route::get('articles','API\V1\SystemController@getArticles')->middleware('permission:article');
        Route::post('guide','API\V1\SystemController@addUserGuide')->middleware('permission:guide');
        Route::get('guides','API\V1\SystemController@getUserGuides')->middleware('permission:guide');
        Route::get('del/guide/{id}','API\V1\SystemController@delUserGuides')->middleware('permission:guide');
        Route::get('orders','API\V1\OrderController@getAllOrders');
        Route::get('reports','API\V1\SystemController@getReports')->middleware('permission:reportlist');
        Route::get('parttimes','API\V1\SystemController@listPartTime')->middleware('permission:parttimelist');
        Route::get('modify/report/{id}','API\V1\SystemController@modifyReport')->middleware('permission:reportlist');
        Route::get('modify/parttime/{id}','API\V1\SystemController@modifyPartTime')->middleware('permission:parttimelist');
        Route::get('sign/activities','API\V1\SystemController@getSignActivities')->middleware('permission:activity');
        Route::get('scan/activities','API\V1\SystemController@getScanActivities')->middleware('permission:activity');
        Route::post('activity/sign','API\V1\SystemController@addSignActivity')->middleware('permission:activity');
        Route::post('activity/scan','API\V1\SystemController@addScanActivity')->middleware('permission:activity');
        Route::get('config','API\V1\SystemController@getSystemConfig')->middleware('permission:config');
        Route::post('config','API\V1\SystemController@modifySystemConfig')->middleware('permission:config');
        Route::get('users','API\V1\UserController@getAllUsers')->middleware('permission:userlist');
        Route::get('refuses','API\V1\SystemController@getRefuseReasons')->middleware('permission:rejectreason');
        Route::post('refuse','API\V1\SystemController@addRefuseReasen')->middleware('permission:rejectreason');
        Route::get('del/refuse/{id}','API\V1\SystemController@delRefuseReason')->middleware('permission:rejectreason');
        Route::get('del/report/{id}','API\V1\SystemController@delReportReason')->middleware('permission:reportreason');
        Route::get('report/reasons','API\V1\SystemController@getReportReasons')->middleware('permission:reportreason');
        Route::post('report/reason','API\V1\SystemController@addReportReason')->middleware('permission:reportreason');
        Route::post('share/activity','API\V1\SystemController@addShareActivity')->middleware('permission:activity');
        Route::get('share/activities','API\V1\SystemController@getShareActivities')->middleware('permission:activity');
        Route::get('pass/commodities','API\V1\CommodityController@getPassCommodities')->middleware('permission:passlist');
        Route::get('unpass/commodities','API\V1\CommodityController@getUnPassCommodities')->middleware('permission:unpasslist');
        Route::get('pass/commodity/{id}','API\V1\CommodityController@passCommodity')->middleware('permission:pass');
        Route::get('modify/user/{id}','API\V1\UserController@modifyUser')->middleware('permission:userenable');
        Route::post('user/level','API\V1\UserController@modifyUserLevel')->middleware('permission:userenable');
        Route::get('role/users/{id}','API\V1\UserController@getRoleUsers')->middleware('permission:attachRole');
        Route::get('del/user/role','API\V1\UserController@delRoleUser')->middleware('permission:attachRole');
        Route::post('add/admin','API\V1\UserController@addAdmin');
        Route::get('user/{id}','API\V1\UserController@getUser');
        Route::get('del/user/{id}','API\V1\UserController@delUser');
    });
});
