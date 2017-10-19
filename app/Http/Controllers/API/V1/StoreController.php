<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Store;
use App\Models\StoreType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    //
    public function getTypes()
    {
        $types = StoreType::all();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$types
        ]);
    }
    public function addType()
    {

    }
    public function getStores()
    {
        $stores = Store::where(['state'=>'1'])->get();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$stores
        ]);
    }
    public function getStore($id)
    {
        $store = Store::find($id);
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$store
        ]);
    }
}
