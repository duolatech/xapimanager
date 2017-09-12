<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\ApiDetail;
use App\Models\Classify;
use App\Models\User;
require_once app_path().'/tools/HttpClient/vendor/autoload.php';
class IndexController extends Controller
{
    //Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=30;
    const CHCHETIME = 5*60;
    protected $cls;// 接口分类
    protected $proid;
    
    public function __construct(Request $request){
    
        //请求数据
        $this->request = $request;
        //当前项目id
        if(!empty($this->request['sys']['Project']['proid'])){
            $this->proid = $this->request['sys']['Project']['proid'];
        }else{
            $this->proid = 0;
        }
        //分类数据
        $this->cls = cache::get('classify');
        
        if(empty($this->cls)){
            $this->cls = Classify::getClassify($this->proid, 0);
            Cache::put('classify', $this->cls, self::CHCHETIME);
        }
    
    }
    public function index(Request $request){
        
        $data = array();
        $colors = array('progress-bar-primary','progress-bar-info',
        'progress-bar-success','progress-bar-warning','progress-bar-danger');
        $data['classify'] = array();
        foreach($this->cls as $value){
            $data['classify'][] = array(
                'id' => $value['id'],
                'classifyname'=>$value['classifyname'],
                'color'=>$colors[array_rand($colors)],
                'ratio'=> rand(20,60)
            );
        }
        return view('index', ['data'=>$data]);
    }
    /**
     * 请求Api城市请求统计量
     */
    public function area(){
        
        $HttpClient = new \Leaps\HttpClient\Adapter\Curl();
        $HttpClient->setOption(CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);
        $api = Config('project.areaNumber');
        $protocol = is_HTTPS() ? 'https://' : 'http://';
        $apiUrl = $protocol.$_SERVER['HTTP_HOST'].$api;
        $response = $HttpClient->get($apiUrl);
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
