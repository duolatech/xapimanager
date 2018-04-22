<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\ApiDetail;
class MockController extends Controller
{
    /**
     * 响应类型(1json,2xml,3jsonp,4html)
     */
    public function index(Request $request){
        
        $uri = trim($request->path(), 'Mock');

        if(!empty($uri)){
            $data = ApiDetail::where('apidetail.URI', 'like', $uri)->first();
        }
        $goback = !empty($data->goback) ? $data->goback : '请在编辑Api时，添加响应数据类型和内容';
        $rtype = !empty($data->response_type) ? $data->response_type : '';
        switch($rtype){
            case 1: $ctype = 'application/json;';break;
            case 2: $ctype = 'text/xml;';break;
            case 3: $ctype = 'application/json;';break;
            case 4: $ctype = 'text/html;';break;
            default: $ctype = 'text/html';break;
        }
        return response($goback)->header('Content-Type', $ctype);
        
    }
}
