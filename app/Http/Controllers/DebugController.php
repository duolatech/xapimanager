<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\debugApiRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\ApiList;
use App\Models\ApiDetail;
use App\Models\ApiParam;
use Input;
class DebugController extends Controller
{
    //Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=30;
    /**
     * debug 调试页面
     * @param Request $request
     * @return 调试页面
     */
    public function index(Request $request){
        
        $did = Input::get('did');
        $data = array();
        $type = array('GET', 'POST', 'PUT', 'DELETE');
        if(!empty($did)){
            
            //接口名称
            $data['detail'] = ApiDetail::where('id', $did)->first();
            $data['detail'] = !empty($data['detail']) ? $data['detail']->toArray() : array();
            if(!empty($data['detail'])){
                $data['apiname'] = ApiList::where('id', $data['detail']['listid'])->value('apiname');
            }
            //接口参数
            $param = ApiParam::where('id', $did)->first();
            $param = !empty($param) ? $param->toArray() : array();
            //常规参数
            $way  = array('request');
            foreach ($way as $value){
				if(!empty($param[$value])){
					$arr[$value] = json_decode($param[$value], true);
					foreach ($type as $vol){
						$data['param'][$vol][$value] =$arr[$value][$vol];
					}
				}
            }
            //header头信息
            $data['param']['HEADER']['request'] =  !empty($param['header']) ? json_decode($param['header'], true) : array();
        }
        
        $result['info'] = !empty($data) ? json_encode($data) : '0';
        $result['type'] = $type;
        
        return view('Debug.index', ['data'=>$result]);
    }
    /**
     * Api调试测试
     */
    public function test(debugApiRequest $request){
        
        $data = $_POST;        
        //请求参数
        $param = array();
        $paramInfo = fieldParamSort($data['param']['request'], 'field');
        foreach ($paramInfo as $value){
            if(!empty($value['field'])){
                $param[$value['field']] = $value['value'];
            }
            
        }
        //header头信息
        $headers = array();
        $headerInfo = fieldParamSort($data['param']['header'], 'field');
        foreach ($headerInfo as $value){
            if(!empty($value['field'])){
                $headers[] = $value['field'].': '.$value['value'];
            }
        }
        //Api请求
        $HttpClient = new \Leaps\HttpClient\Adapter\Curl();
        $HttpClient->setOption(CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);
        if(!empty($headers)){
            $HttpClient->setOption(CURLOPT_HTTPHEADER, $headers);
        }
        
        $api  = $data['apiurl'];
        switch($data['type']){
            case 'GET':
                $api = urlSplice($api, $param);
                $response = $HttpClient->get($api);
                break;
            case 'POST':
                $response = $HttpClient->post($api, $param);
                break;
            case 'PUT':
                $response = $HttpClient->put($api, $param);
                break;
            case 'DELETE':
                $api = urlSplice($api, $param);
                $response = $HttpClient->delete($api)->getContent();
                break;
            default:break;
        }
        
        //获取解析过的响应头
        $rheader = $response->getHeaders();
        $status = $response->getStatusCode();
        unset($rheader[0]);
        $result = array(
            'status'    => $status,
            'data'  => array(
                'ContentType'   => $response->getContentType(),
                'header'    => array_merge(array('StatusCode'=>$status),$rheader),
                'runtime'   => $response->getTime(),
                'content'   => $response->getContent()
            )
        );
        
        //请求数据
        $apiUri = parse_url($data['apiurl']);
        if(empty($apiUri['path'])) $apiUri['path'] = '/';
        $result['data']['info'] = array(
            'type'  => $data['type'],
            'detail'    => array(
                'gateway'=>$data['apiurl'],
                'URI'   => $apiUri['path']
            ),
            'name' => (!empty($data['apiname']) && $data['apiname']!='接口信息') ? $data['apiname'] : $apiUri['path'],
            'apiname'   => !empty($data['apiname']) ? $data['apiname'] : '接口信息',
            'param' =>array(
                $data['type'] => array('request' => $paramInfo),
                'HEADER'=>array('request' => $headerInfo)
            ),
        );
        
        return response()->json($result);
        
    }
}
