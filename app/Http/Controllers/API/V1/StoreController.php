<?php

namespace App\Http\Controllers\API\V1;

use App\Models\CommodityType;
use App\Models\Store;
use App\Models\StoreType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class StoreController extends Controller
{
    //获取所有类型
    public function getTypes()
    {
        $types = StoreType::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$types
        ]);
    }
    //添加类型
    public function addType()
    {
        $id = Input::get('id');
        $type = CommodityType::find($id);
        if (empty($type)){
            $type = new CommodityType();
        }
        $type->title = Input::get('title');
        $type->description = Input::get('description');
        if ($type->save()){
            return response()->json([
                'return_code'=>'SUCCESS'
            ]);
        }
    }

    /**
     * 修改类型状态
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function modifyType($id)
    {
        $type = CommodityType::find($id);
        if (empty($type)){
            return response()->json([
                'return_code'=>'FAIL'
            ]);
        }
        if ($type->state == 0){
            $type->state = 1;
        }else{
            $type->state = 0;
        }
        $type->save();
        return response()->json([
            'return_code'=>'SUCCESS'
        ]);
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStores()
    {
        $stores = Store::where(['state'=>'1'])->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$stores
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStore($id)
    {
        $store = Store::find($id);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$store
        ]);
    }
}
