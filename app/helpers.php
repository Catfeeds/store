<?php

if (!function_exists('createNonceStr')){
    function createNonceStr($length = 15) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str.time();
    }
}
if (!function_exists('setUserToken')){
    function setUserToken($key,$value)
    {
        \Illuminate\Support\Facades\Redis::set($key,$value);
    }
}
if (!function_exists('getUserToken')) {
    function getUserToken($key)
    {
        $uid = \Illuminate\Support\Facades\Redis::get($key);
        if (!isset($uid)){
            return false;
        }
        return $uid;
    }
}

if (!function_exists('getCountSql')) {
    function getCountSql($user_id,$start,$end)
    {
        return "SELECT DATE_FORMAT(`created_at`,'%d') as date FROM signs WHERE `user_id` ="." $user_id"." AND `created_at` BETWEEN '".$start."' AND '".$end."'";
    }
}
if (!function_exists('getCityCountSql')) {
    function getCityCountSql($city_id)
    {
        $city_id = implode(',',$city_id);
        return "SELECT COUNT(*) as number FROM commodities WHERE `city_id` in("." $city_id".") group by `city_id`";
    }
}
if (!function_exists('setCode')){
    function setCode($key,$value)
    {
        \Illuminate\Support\Facades\Redis::set($key,$value);
        \Illuminate\Support\Facades\Redis::expire($key,300);

    }
}
if (!function_exists('getCode')) {
    function getCode($phone)
    {
        $code = \Illuminate\Support\Facades\Redis::get($phone);
        if (!isset($code)){
            return false;
        }
        return unserialize($code);
    }
}
if (!function_exists('sendSMS')) {
    function sendSMS($number,$code,$data)
    {
        $sms = new \App\Libraries\AliyunSMS();
        $data = $sms->send($number,\config('alisms.company'),json_encode($data),$code);
        if($data){
            $data = json_decode($data);
            if ($data->success=='true'){
                return true;
            }
            return false;
        }
        return false;
    }
}
if (!function_exists('formatUrl')) {
    function formatUrl($url)
    {
        return env('APP_URL').$url;
    }
}

if (!function_exists('getAround')){
     function getAround($lat,$lon,$raidus){
        $PI = 3.14159265;

        $latitude = $lat;
        $longitude = $lon;

        $degree = (24901*1609)/360.0;
        $raidusMile = $raidus;

        $dpmLat = 1/$degree;
        $radiusLat = $dpmLat*$raidusMile;
        $minLat = $latitude - $radiusLat;
        $maxLat = $latitude + $radiusLat;

        $mpdLng = $degree*cos($latitude * ($PI/180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng*$raidusMile;
        $minLng = $longitude - $radiusLng;
        $maxLng = $longitude + $radiusLng;
        return [
            'minLat'=>round($minLat,7),
            'maxLat'=>round($maxLat,7),
            'minLng'=>round($minLng,7),
            'maxLng'=>round($maxLng,7),
        ];
    }
}
if (!function_exists('calculateDistance')){
    function calculateDistance($lat1,$lon1,$lat2,$lon2,$radius=6378.135){
        $rad = doubleval(M_PI/180.0);
        $lat1 = doubleval($lat1) * $rad;
        $lon1 = doubleval($lon1) * $rad;
        $lat2 = doubleval($lat2) * $rad;
        $lon2 = doubleval($lon2) * $rad;

        $theta = $lon2 - $lon1;
        $dist = acos(sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) * cos($theta));
        if($dist < 0) {
            $dist += M_PI;
        }
        // 单位为 千米
        return $dist = $dist * $radius;
    }
}

if (!function_exists('getRandCode')){
    function getRandCode($length = 6)
    {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
}
if (!function_exists('push')){
    function push($uid,$alias_type,$title,$content,$subtitle='')
    {
        $android_predefined = [
            'ticker' => 'android ticker',
            'title' => $title,
            'text' => $content,
            'play_vibrate' => 'true',
            'play_lights' => 'true',
            'play_sound' => 'true',
            'after_open' => 'go_activity',
            'activity' => 'com.sennki.flybrid.main.user.UserMyMessageActivity'
        ];
        $customField = array(); //oth
        \Zzl\Umeng\Facades\Umeng::android()->sendCustomizedcast($uid,$alias_type,$android_predefined,$customField);
//        dd($data);
        $predefined = [
            'alert'=>[
                'title'=>$title,
                'subtitle'=>$subtitle,
                'body'=>$content
            ]
        ];
        \Zzl\Umeng\Facades\Umeng::ios()->sendCustomizedcast($uid,$alias_type,$predefined,$customField);
    }
}