<?php

namespace App\Http\Controllers\API\V1;

use App\Models\MemberLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Zizaco\Entrust\Entrust;
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
    public function getPermissions($id)
    {
        $role = EntrustRole::find($id);
        $permissions = $role->perms();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$permissions
        ]);
    }
    public function attachPermission()
    {
        
    }
}
