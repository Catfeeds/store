<?php

namespace App\Http\Controllers\API\V1;

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
}
