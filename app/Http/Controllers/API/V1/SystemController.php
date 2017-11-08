<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Advert;
use App\Models\City;
use App\Models\MemberLevel;
use App\Models\Message;
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
        $id = Input::get('id');
        $level = Input::get('level');
        if (empty($id)){
            $member = new MemberLevel();
            $count = MemberLevel::where('level','=',$level)->count();
            if ($count>=1){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>'不能添加重复的会员等级'
                ]);
            }
            $member->level = Input::get('level');
            $member->title = Input::get('title');
            $member->price = Input::get('price');
            $member->time = Input::get('time');
            $member->send_daily = Input::get('send_daily');
            $member->send_max = Input::get('send_max');
        }else{
            $member = MemberLevel::find($id);
            $count = MemberLevel::where('level','=',$level)->count();
            if ($count>=1){
                return response()->json([
                    'return_code'=>'FAIL',
                    'return_msg'=>'不能添加重复的会员等级'
                ]);
            }
            $member->level = Input::get('level');
            $member->title = Input::get('title');
            $member->price = Input::get('price');
            $member->time = Input::get('time');
            $member->send_daily = Input::get('send_daily');
            $member->send_max = Input::get('send_max');
        }

        if ($member->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getMemberLevels()
    {
        $level = MemberLevel::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$level
        ]);
    }
    public function getMemberLevel($id)
    {
        $level = MemberLevel::find($id);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$level
        ]);
    }
    public function delMemberLevel($id)
    {
        $level = MemberLevel::find($id);
        if ($level->delete()){
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
            $pres = Input::get('pres');
            if (!empty($pres)){
                for ($i=0;$i<count($pres);$i++){
                    $permission = EntrustPermission::find($pres[$i]);
                    $role->attachPermission($permission);
                }
            }
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function delRole($id)
    {
        $role = EntrustRole::find($id);
        $role->delete(); // This will work no matter what
// Force Delete
        $role->users()->sync([]); // Delete relationship data
        $role->perms()->sync([]); // Delete relationship data
        $role->forceDelete();
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);
    }
    public function getRoles()
    {
        $roles = EntrustRole::all();
        $count = EntrustRole::count();
        for ($i=0;$i<count($roles);$i++){
            $roles[$i]->perms = $roles[$i]->perms()->get();
        }
        return response()->json([
            'return_code'=>'SUCCESS',
            'count'=>$count,
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
        $cities = City::where('pid','=',0)->get();
        for ($i=0;$i<count($cities);$i++){
                $cities[$i]->cities = City::where('pid','=',$cities[$i]->id)->get();
        }
        return response()->json($cities);
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
    public function getMessages()
    {
        $uid = getUserToken(Input::get('token'));
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $messages = Message::where('receive_id','=',$uid)->limit($limit)->offset(($page-1)*$limit)->orderBy('read','ASC')->orderBy('id','DESC')->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$messages
        ]);
    }
    public function getMessage($id)
    {
        $message = Message::find($id);
        $message->read = 1;
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$message
        ]);
    }
    public function readMessage($id)
    {
        $message = Message::find($id);
        $message->read = 1;
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$message
        ]);
    }
    public function delMessage($id)
    {
        $uid = getUserToken(Input::get('token'));
        $message = Message::find($id);
        if ($message->receive_id != $uid){
            return response()->json([
                'return_code'=>'FAIL',
                'return_msg'=>'无权操作！'
            ]);
        }
        if ($message->delete()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    public function addAdvert()
    {
        $id = Input::get('id');
        if ($id){
            $advert = Advert::find($id);
        }else{
            $advert = new Advert();
        }
        $advert->title = Input::get('title');
        $advert->city_id = Input::get('city_id');
        $advert->url = Input::get('url');
        $advert->link_url = Input::get('link_url');
        if ($advert->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }
    public function getAdverts()
    {
        $type = Input::get('type');
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $adverts = Advert::where([
            'type'=>$type,
            'state'=>'1'
        ])->limit($limit)->offset(($page-1)*$limit)->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$adverts
        ]);
    }

    public function getAllAdverts()
    {
        $type = Input::get('type');
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $adverts = Advert::where([
            'type'=>$type
        ])->limit($limit)->offset(($page-1)*$limit)->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$adverts
        ]);
    }
}
