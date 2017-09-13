<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\debugApiRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\ApiList;
use App\Models\ApiDetail;
use App\Models\ApiParam;
use App\Models\Domain;
use App\Models\Debug;
use Input;
require_once app_path().'/tools/HttpClient/vendor/autoload.php';
class DebugController extends Controller
{
    //Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=10;
    protected $uid;
    protected $type;
    
    public function __construct(){
        
        $this->type = array(1=>'GET', 2=>'POST', 3=>'PUT', 4=>'DELETE');
        
    }
    /**
     * debug 调试页面
     * @param Request $request
     * @return 调试页面
     */
    public function index(Request $request){
        
        $this->uid = Session::get('uid');
        
        $data = array();
        $data['param']['header'] = array(1,2);
        $data['param']['request'] = array(1,2);
        if(!empty(Input::get('did'))){
            $did = Input::get('did');
            //接口名称
            $data['detail'] = ApiDetail::where('id', $did)->first();
            $data['detail'] = !empty($data['detail']) ? $data['detail']->toArray() : array();
            if(!empty($data['detail'])){
                $data['apiname'] = ApiList::where('id', $data['detail']['listid'])->value('apiname');
            }
            //接口参数
            $data['param']['header'] = !empty($data['detail']['header']) ? json_decode($data['detail']['header'], true) : array();
            $data['param']['request'] = !empty($data['detail']['request']) ? json_decode($data['detail']['request'], true) : array();
        }
        if(!empty(Input::get('sid'))){
            $sid = Input::get('sid');
            $recordData = Debug::where(['uid'=>$this->uid, 'id'=>$sid])->first();
            if(!empty($recordData)){
                $recordData = $recordData->toArray();
                $data['detail']['gateway'] = $recordData['apiurl'];
                $rheader = json_decode($recordData['header'], true);
                $rrequest  = json_decode($recordData['param'], true);
                $data['param']['header'] = array();
                $data['param']['request'] = array();
                foreach ($rheader as $key=>$value){
                    $data['param']['header'][] = array(
                        'field' => $key,
                        'fieldType' => 1,
                        'must'  => 1,
                        'value' => $value,
                    );
                }
                foreach ($rrequest as $key=>$value){
                    $data['param']['request'][] = array(
                        'field' => $key,
                        'fieldType' => 1,
                        'must'  => 1,
                        'value' => $value,
                    );
                }
            }else{
                $recordData = array();
            }
            
        }
        //服务器IP绑定信息及保存记录
        if(!empty($this->uid)){
            $domain = Domain::where(['uid'=>$this->uid])->get();
            $record = Debug::where(['uid'=>$this->uid])->get();
        }
        $domain = !empty($domain) ? $domain->toArray() : array();
        $record = !empty($record) ? $record->toArray() : array();
        foreach ($domain as &$value){
            if(!empty($value)){
                $value['iplong'] = json_decode($value['iplong'], true);
                $long = array();
                if(!empty($value['iplong'])){
                    foreach ($value['iplong'] as $vol){
                        $long[] = long2ip($vol['iplong']);
                    }
                }
                $value['ips'] = implode(',', $long);
            }
        }
        //保存记录
        foreach ($record as &$vol){
            $path = parse_url($vol['apiurl']);
            $vol['path'] = !empty($path['path']) ? $path['path'] : '/';
            $vol['typeName'] = $this->type[$vol['type']];
        }
        
        $result = array(
            'info'  => !empty($data) ? json_encode($data) : '0',
            'type'  => $this->type,
            'domain'    => $domain,
            'record'    => $record,
            'param'     => $data['param'],
            'apiurl'    => !empty($data['detail']['gateway']) ? $data['detail']['gateway'] : ''
        );
        
        return view('debug.index', ['data'=>$result]);
    }
    /**
     * Api调试测试
     */
    public function test(debugApiRequest $request){
        
        $data = $_POST;        
        //请求参数
        $param = array();
        $paramInfo = array();
        if(!empty($data['param']['request'])){
            $paramInfo = fieldParamSort($data['param']['request'], 'field');
            foreach ($paramInfo as $value){
                if(!empty($value['field'])){
                    $param[$value['field']] = $value['value'];
                }
            }
        }
        //请求链接
        $api  = $data['apiurl'];
        $domip = $this->getDomainIp($api);
        
        //header头信息
        $headers = array();
        $headerInfo = array();
        if(!empty($data['param']['header'])){
            $headerInfo = fieldParamSort($data['param']['header'], 'field');
            foreach ($headerInfo as $value){
                if(!empty($value['field'])){
                    $headers[] = $value['field'].': '.$value['value'];
                }
            }
        }
        //Api请求
        $dcookies = Session::get('dcookies');
        $HttpClient = new \Leaps\HttpClient\Adapter\Curl();
        $HttpClient->setOption(CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);
        if(!empty($dcookies) && is_array($dcookies)){
            $HttpClient->setCookie(implode(";", $dcookies));
        }
        if(!empty($domip)){
            $HttpClient->setHostIp($domip);
        }
        if(!empty($headers)){
            $HttpClient->setOption(CURLOPT_HTTPHEADER, $headers);
        }
        
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
                'content'   => $response->getContent(),
                'rawContent' => htmlspecialchars($response->getContent())
            )
        );
        Session::put('dcookies', $response->getCookies());
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
    /**
     * 服务器ip绑定
     */
    public function domain(){
        
        $id = Input::get('domid');
        $domain = Input::get('domain');
        $ips = Input::get('ips');
        
        //IP地址判断
        $flag = true;
        $gather = explode(',', $ips);
        foreach($gather as $key=>$ip){
            if(!filter_var($ip, FILTER_VALIDATE_IP)) {
                $flag = false;
            }
            $long[] = array(
                'status' => $key==0 ? 1 : 0,
                'iplong' => sprintf('%u',ip2long($ip))
            );
        }
        if(!$flag){
            return response()->json(['status'=>4010, 'message'=>'IP错误，请重新输入']);
        }
        $dom = Domain::find($id);
        if(empty($dom)){
            $dom = new Domain();
        }else{
            $dom->id = $id;
        }
        $dom->uid = Session::get('uid');;
        $dom->domain = $domain;
        $dom->iplong = json_encode($long, JSON_UNESCAPED_UNICODE);
        $dom->ctime = time();
        $dom->save();
        
        if($dom->id){
            return response()->json(['status'=>200, 'message'=>'保存成功']);
        }else{
            return response()->json(['status'=>4011, 'message'=>'保存失败，请稍后重试']);
        }
    }
    /**
     * 指定域名绑定的IP
     * @param $domid 
     */
    public function isBind(){
        
        $id = Input::get('domid');
        $iplong = Input::get('iplong');
        $this->uid = Session::get('uid');
        
        $dom = Domain::find($id);
        if(!empty($dom) && !empty($this->uid)){
            $ips = $dom->where(['uid'=>$this->uid])->value('iplong');
            if(!empty($ips)){
                $ips = json_decode($ips,true);
                foreach ($ips as $ip){
                    $long[] = array(
                        'status' => ($ip['iplong'] == $iplong) ? 1 : 0,
                        'iplong' => $ip['iplong']
                    );
                }
            }
            $dom->iplong = json_encode($long, JSON_UNESCAPED_UNICODE);
            $dom->save();
        }
        return response()->json(['status'=>200, 'message'=>'保存成功']);
    }
    /**
     * 获取域名对应的IP
     * apiUrl 接口链接
     */
    public function getDomainIp($apiUrl){
        
        $this->uid = Session::get('uid');
        if(!empty($this->uid)){
            $domain = Domain::where(['uid'=>$this->uid])->get();
        }
        $domain = !empty($domain) ? $domain->toArray() : array();
        $url = parse_url($apiUrl);
        $ip = '';
        if(!empty($url)){
            foreach ($domain as $value){
                if(!empty($url['host']) && $url['host']==trim($value['domain'])){
                    $iplong = $value['iplong'];
                    $iplong = json_decode($iplong, true);
                    foreach ($iplong as $vol){
                        if($vol['status']==1){
                            $ip = long2ip($vol['iplong']);
                        }
                    }
                }
            }
        }
        
        return $ip;
    }
    /**
     * Api保存
     */
    public function store(){
        
        $data = $_POST;
        //请求参数
        $param = array();
        $paramInfo = fieldParamSort($data['param']['request'], 'field');
        foreach ($paramInfo as $value){
            if(!empty($value['field'])){
                $param[$value['field']] = $value['value'];
            }
        }
        //请求链接
        $apiUrl  = $data['apiurl'];
        $url = parse_url($apiUrl);
        
        //header头信息
        $headers = array();
        $headerInfo = fieldParamSort($data['param']['header'], 'field');
        foreach ($headerInfo as $value){
            if(!empty($value['field'])){
                $headers[$value['field']] = $value['value'];
            }
        }
        
        $debug = new Debug();
        $debug->uid = Session::get('uid');
        $debug->type = array_search($data['type'], $this->type);
        $debug->apiurl = $data['apiurl'];
        $debug->param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $debug->header = json_encode($headers, JSON_UNESCAPED_UNICODE);
        $debug->addtime = time();
        $debug->save();
        
        if($debug->id){
            $data = array(
                'id'   =>$debug->id, 
                'path' => !empty($url['path']) ? $url['path'] : '/',
                'type' => $data['type']
            );
            return response()->json(['status'=>200, 'message'=>'保存成功', 'data'=>$data]);
        }else{
            return response()->json(['status'=>4011, 'message'=>'保存失败，请稍后重试']);
        }
    }
    /**
     * 删除保存的api
     */
    public function del(){
        
        $id = Input::get('id');
        $uid = Session::get('uid');
        
        if(!empty($id)){
            $deleted = Debug::where(['id'=>$id,'uid'=>$uid])->delete();
        }
        if(!empty($deleted)){
            return response()->json(['status'=>200, 'message'=>'删除成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'删除失败，请稍后重试']);
        }
    }
}
