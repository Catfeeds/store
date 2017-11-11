<?php

namespace App\Http\Controllers\API\V1;

use App\Models\LaunchImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

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
    public function addLaunchImage()
    {
        $id = Input::get('id');
        if ($id){
            $image = LaunchImage::find($id);
        }else{
            $image = new LaunchImage();
        }

        $image->title = Input::get('title');
        $image->url = Input::get('url');
        $image->link_url = Input::get('link_url');
        if ($image->save()){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }
    }
    public function getLaunchImages()
    {
        $images = LaunchImage::all();
        return response()->json([
            'return_code'=>"SUCCESS",
            'data'=>$images
        ]);
    }
    public function enableLauncherImage($id)
    {
        LaunchImage::where('state','=',1)->update([
            'state'=>'0'
        ]);
        $image = LaunchImage::find($id);
        $image->state = 1;
        $image->save();
        return response()->json([
            'return_code'=>"SUCCESS"
        ]);
    }
    public function delLauncherImage($id)
    {
        $image = LaunchImage::find($id);
        if ($image->delete()){
            return response()->json([
                'return_code'=>"SUCCESS"
            ]);
        }
    }
}
