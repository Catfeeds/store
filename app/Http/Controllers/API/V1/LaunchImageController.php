<?php

namespace App\Http\Controllers\API\V1;

use App\Models\LaunchImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LaunchImageController extends Controller
{
    //
    public function getLaunchImage()
    {
        $image = LaunchImage::where([
            'state'=>'1'
        ])->first();
        return response()->json([
            'return_code'=>'SUCCESS',
            'data'=>$image
        ]);
    }
}
