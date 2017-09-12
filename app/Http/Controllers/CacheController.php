<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Input;
class CacheController extends Controller
{
    public function index(Request $request){
        
        $key = Input::get('clear');
        if(!empty($key)){
            //清除某一缓存
            Cache::forget($key);
        }else{
            //清除所有缓存
            Cache::flush();
        }
        
        return response()->json(['status'=>200, 'message'=>'清除成功']);
        
    }
}
