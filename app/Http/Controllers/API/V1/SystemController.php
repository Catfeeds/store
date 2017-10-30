<?php

namespace App\Http\Controllers\API\V1;

use App\Models\City;
use App\Models\MemberLevel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Zizaco\Entrust\Entrust;
use Zizaco\Entrust\EntrustPermission;
use Zizaco\Entrust\EntrustRole;

class SystemController extends Controller
{
    //
    public function addMemberLevel()
    {
        $member = new MemberLevel();
        $member->level = Input::get('level');
        $member->title = Input::get('title');
        $member->price = Input::get('price');
        $member->time = Input::get('time');
        $member->send_daily = Input::get('send_daily');
        $member->send_max = Input::get('send_max');
        if ($member->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function addRole()
    {
        $role = new EntrustRole();
        $role->name = Input::get('name');
        $role->display_name = Input::get('display_name');
        $role->description = Input::get('description');
        if ($role->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getRoles()
    {
        $roles = EntrustRole::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$roles
        ]);
    }
    public function getRole($id)
    {
        $roles = EntrustRole::find($id);
        $permission = $roles->perms()->get();
        $user = $roles->users()->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>[
                'role'=>$roles,
                'permission'=>$permission,
                'user'=>$user
            ]
        ]);
    }
    public function getPermissions()
    {
        $permissions = EntrustPermission::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$permissions
        ]);
    }
    public function addPermission()
    {
        $permission = new EntrustPermission();
        $permission->name = Input::get('name');
        $permission->display_name = Input::get('display_name');
        $permission->description = Input::get('description');
        if ($permission->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function attachPermission()
    {
        $role = EntrustRole::find(Input::get('role'));
        $permission = EntrustPermission::find(Input::get('permission'));
        $role->attachPermission($permission);
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);
    }
    public function test()
    {
        $user = User::find(7);
        if ($user->can('addRole')){
            return "YES";
        }
        return "NO";
    }
    public function getCities()
    {
        $cities = City::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$cities
        ]);
    }
    public function setCity()
    {
        $cities = City::where('pid','!=',0)->get();
        for ($i=0;$i<count($cities);$i++){
            $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$cities[$i]->name.'&output=json&ak=ghjW6DPclbHFsGSxdkwp3GWczKSmjT3f';
            $data = $this->getCityInfo($url);
            $result = $data['result']['location'];
            $cities[$i]->latitude = $result['lat'];
            $cities[$i]->longitude = $result['lng'];
            $cities[$i]->save();
        }
        return "SUCCESS";
    }
    public function getCityInfo($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if($output === FALSE ){
            return false;
        }
        curl_close($curl);
        return json_decode($output,JSON_UNESCAPED_UNICODE);
    }
}
