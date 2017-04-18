<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ApiDetail;
use App\Models\Classify;
use App\Models\User;
class IndexController extends Controller
{
    //Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=30;
    public function index(Request $request){
        
        return view('index');
    }
    /**
     * 请求Api城市请求统计量
     */
    public function area(){
        
        $HttpClient = new \Leaps\HttpClient\Adapter\Curl();
        $HttpClient->setOption(CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);
        $api = Config('project.areaNumber');
        $response = $HttpClient->get($api);
        $status = $response->getStatusCode();
        $data = $response->getContent();
        $info = array();
        if(!empty($data)){
            $info = json_decode($data, true);
        }
        $arr = array();
        if(!empty($info['data']) && is_array($info['data'])){
            foreach ($info['data'] as $value){
                $arr[] = array(
                    'area_id' => $value['area_id'],
                    'name'  => trim($value['area_name']),
                    'value' => $value['number'],
                    'longitude' => $value['longitude'],
                    'latitude' => $value['latitude'],
                );
            }
        }
        $result = array(
            'status'    => 200,
            'message'   => '成功',
            'data'  => $arr
        );
        
        return response()->json($result);
        
    }
}
