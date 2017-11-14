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

Route::group(['middleware'=>'cross'],function (){
    Route::any('upload','API\V1\UploadController@uploadImage');
    Route::post('login','API\V1\UserController@adminLogin');
    Route::get('test',function (){
        $commodity = \App\Models\Commodity::find(1);
        $d = [
            'date'=>$commodity->created_at->format('Y-m-d')
        ];
        $data = sendSMS('18664894928','SMS_109450243',$d);
        dd($data);
    });
    Route::group(['middleware'=>'auth'],function (){

        Route::get('launcher/images','API\V1\LaunchImageController@getLaunchImages');
        Route::get('enable/launcher/image/{id}','API\V1\LaunchImageController@enableLauncherImage');
        Route::post('launcher/image','API\V1\LaunchImageController@addLaunchImage');
        Route::get('del/image/{id}','API\V1\LaunchImageController@delLauncherImage');
        Route::post('store/type','API\V1\StoreController@addType');
        Route::get('modify/store/type/{id}','API\V1\StoreController@modifyType');
        Route::post('member/level','API\V1\SystemController@addMemberLevel');
        Route::get('member/levels','API\V1\SystemController@getMemberLevels');
        Route::get('member/level/{id}','API\V1\SystemController@getMemberLevel');
        Route::get('del/member/level/{id}','API\V1\SystemController@delMemberLevel');
        Route::post('role','API\V1\SystemController@addRole');
        Route::get('roles','API\V1\SystemController@getRoles');
        Route::get('role/{id}','API\V1\SystemController@getRole');
        Route::get('del/role/{id}','API\V1\SystemController@delRole');
        Route::get('permissions','API\V1\SystemController@getPermissions');
        Route::post('permission','API\V1\SystemController@addPermission');
        Route::post('attach/permission','API\V1\SystemController@attachPermission');
        Route::post('type','API\V1\CommodityController@addCommodityType');
        Route::get('types','API\V1\CommodityController@getTypes');
        Route::get('modify/type/{id}','API\V1\CommodityController@delType');
        Route::post('qrcode/{id}','API\V1\SystemController@addQrCode');
        Route::get('qrcode','API\V1\SystemController@getQrCode');
        Route::post('advert','API\V1\SystemController@addAdvert');
        Route::get('adverts','API\V1\SystemController@getAllAdverts');
        Route::get('del/advert/{id}','API\V1\SystemController@delAdvert');
        Route::get('cities','API\V1\SystemController@getCities');
        Route::post('article','API\V1\SystemController@addArticle');
        Route::get('articles','API\V1\SystemController@getArticles');
        Route::post('guide','API\V1\SystemController@addUserGuide');
        Route::get('guides','API\V1\SystemController@getUserGuides');
        Route::get('del/guide/{id}','API\V1\SystemController@delUserGuides');
        Route::get('orders','API\V1\OrderController@getAllOrders');
        Route::get('reports','API\V1\SystemController@getReports');
        Route::get('parttimes','API\V1\SystemController@listPartTime');
        Route::get('modify/report/{id}','API\V1\SystemController@modifyReport');
        Route::get('modify/parttime/{id}','API\V1\SystemController@modifyPartTime');
        Route::get('activity/sign','API\V1\SystemController@getSignActivity');
        Route::get('activity/scan','API\V1\SystemController@getScanActivity');
        Route::post('activity/sign','API\V1\SystemController@addSignActivity');
        Route::post('activity/scan','API\V1\SystemController@addScanActivity');
        Route::get('config','API\V1\SystemController@getSystemConfig');
        Route::post('config','API\V1\SystemController@modifySystemConfig');
        Route::get('users','API\V1\UserController@getAllUsers');
        Route::get('refuses','API\V1\SystemController@getRefuseReasons');
        Route::post('refuse','API\V1\SystemController@addRefuseReasen');
        Route::get('del/refuse/{id}','API\V1\SystemController@delRefuseReason');
        Route::get('del/report/{id}','API\V1\SystemController@delReportReason');
        Route::get('report/reasons','API\V1\SystemController@getReportReasons');
        Route::post('report/reason','API\V1\SystemController@addReportReason');
        Route::post('share/activity','API\V1\SystemController@getShareActivities');
        Route::get('share/activity','API\V1\SystemController@getShareActivities');
        Route::get('pass/commodities','API\V1\CommodityController@getPassCommodities');
        Route::get('unpass/commodities','API\V1\CommodityController@getUnPassCommodities');
        Route::get('pass/commodity/{id}','API\V1\CommodityController@passCommodity');
        Route::get('modify/user/{id}','API\V1\UserController@modifyUser');

    });



});
