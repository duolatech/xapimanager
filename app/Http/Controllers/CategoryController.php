<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\classifyRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classify;
use App\Models\ApiList;
use App\Models\ApiParam;
use Input, Log;
class CategoryController extends Controller
{
    protected $proid;
    protected $request;
    
    public function __construct(Request $request){
        
        //请求数据
        $this->request = $request;
        //当前项目id
        if(!empty($this->request['sys']['Project']['proid'])){
            $this->proid = $this->request['sys']['Project']['proid'];
        }else{
            $this->proid = 0;
        }
    }
    /**
     * 获取分类信息页面
     */
    public function index(){
        
        $info = Classify::getClassify($this->proid, 0);
        
        return view('category.index', ['info'=>$info]);
    }
    /**
     * 添加分类
     * @return 分类视图
     */
    public function infoCategory(){
        
        $classifyId = Input::get('classifyId');
        $data = Classify::where(['id'=>$classifyId, 'status'=>1])->first();
        $info = !empty($data) ? $data->toArray() : array();
        //查询负责人信息
        if(!empty($info['leader'])){
            $userinfo = User::whereIn('uid', explode(',', $info['leader']))->get();
            $info['user'] = !empty($userinfo) ? $userinfo->toArray() : array();
        }
        
        return view('category.info', ['info'=>$info]);
    }
    /**
     * 添加子分类
     * @return 分类视图
     */
    public function infoSubCategory(){
        
        $classifyId = Input::get('subClassifyId');
        $data = Classify::where(['id'=>$classifyId, 'status'=>1])->first();
        $info = !empty($data) ? $data->toArray() : array();
        //查询负责人信息
        if(!empty($info['leader'])){
            $userinfo = User::whereIn('uid', explode(',', $info['leader']))->get();
            $info['user'] = !empty($userinfo) ? $userinfo->toArray() : array();
        }
        //查询所有分类
        $all = Classify::where('pid',0)->where('status', 1)->get();
        $info['classify'] = !empty($all) ? $all->toArray() : array();
        $info['currentClassify'] = Input::get('classifyId');
        
        return view('category.infoSub', ['info'=>$info]);
    }
    /**
     * 分类存储
     * @return 分类视图
     */
    public function categoryStore(classifyRequest $request){
        
        $pid = Input::get('pid');
        $classifyname = Input::get('classify');
        $classifyId = Input::get('classifyId');
        $description = Input::get('description');
        $members = trim(Input::get('members'), ',');
        $classify = array();
        if(!empty($classifyId)){
            $classify = Classify::find($classifyId);
        }
        if(empty($classify)){
            $classify = new Classify();
            $classify->addtime = time();
            $classify->creator = session::get('uid');
        }
        $classify->proid = $this->proid;
        $classify->classifyname = $classifyname;
        $classify->pid = $pid;
        $classify->description = $description;
        $classify->leader = $members;
        $classify->status = 1;
        $classify->save();
        Cache::forget('classify');
        if(!empty($classify->id)){
            return response()->json(['status'=>200, 'message'=>'保存成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'用户名不存在，请核对和输入']);
        }
    }
    /**
     * 查询分类信息
     * @param $cid 分类id
     */
    public function getClassify($cid){
        
        $data = Classify::where(['id'=>$cid, 'status'=>1])->first();
        $info = !empty($data) ? $data->toArray() : array();
        
        return $info;
    }
    /**
     * 查询分类接口信息并导出
     * @param $cid 分类id
     */
    public function subClassify(Request $request, $cid){
        
        //查询分类信息
        $class = $this->getClassify($this->proid, $cid);
        //子分类id
        $subIds = array($cid);
        //分类接口详情信息
        $param = array(
            'envid' =>intval(Input::get('envid')),
            'classify' => $subIds,
        );
        $status = array(1,2,3);
        $alt = new ApiList();
        $list = $alt->getApiDetail($param, $status ,0, 500);
        
        //分类参数信息
        $dids = array();
        foreach($list['info'] as $value){
            $dids[] = $value['id'];
        }
        //接口参数
        $paramtype = array(1=>'GET',2=>'POST',3=>'PUT',4=>'DELETE');
        foreach($list['info'] as &$vol){
            $type = !empty($paramtype[$vol['type']]) ? $paramtype[$vol['type']] : '1';
            $vol['param'] = array(
                $type => array(
                    'request' => json_decode($vol['request'], true),
                    'response' => json_decode($vol['response'], true),
                ),
                'HEADER' => array(
                    'request' => json_decode($vol['header'], true)  
                ),
                'statuscode' => json_decode($vol['statuscode'], true),
            );
        }
        $list['classify'] = $class;
        $list['site'] = $request['sys']['Website'];
        $list['time'] = date('Y-m-d', time());
        
        //dd($list);
        //输出doc文件
        return view('Category.doc', ['data'=>$list]);
        
    }
    /**
     * 批量获取Api参数信息
     * @param array $dids Api详情id
     */
    public function getParam(array $dids){
        
        $data = array();
        $type = array('GET', 'POST', 'PUT', 'DELETE');
        if(!empty($dids)){
            $apiParam = ApiParam::whereIn('detailid', $dids)->get();
            $apiParam = !empty($apiParam) ? $apiParam->toArray() : array();
            foreach ($apiParam as $param){
                //常规参数
                $res = array();
                $way  = array('request', 'response');
                foreach ($way as $value){
                    $arr[$value] = json_decode($param[$value], true);
                    foreach ($type as $vol){
                        $only = $arr[$value][$vol];
                        $param_info = $this->filter($only);
                        if(!empty($param_info)){
                            $res[$vol][$value] = $param_info;
                        }
                    }
                }
                //每种type都需要request、response
                foreach ($res as &$rex){
                    foreach ($way as $value){
                        if(empty($rex[$value])){
                            $rex[$value] = array();
                        }
                    }
                }
                //header头信息
                $header = json_decode($param['header'], true);
                $res['HEADER'] = array(
                    'request'   => $this->filter($header),
                );
                //状态码
                $res['statuscode'] =  json_decode($param['statuscode'], true);
        
                $data[$param['detailid']] = $res;
            }
        }
        return $data;
    }
    /**
     * 过滤参数中的空字段
     * @param $data 参数信息
     */
    public function filter($data){
        
        if(is_array($data)){
            foreach($data as $key=>$value){
                $field = trim($value['field']);
                if(empty($field)){
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
    /**
     * 获取分类详情
     */
    public function getDetail(){
        
        $classifyId = Input::get('classifyId');
        $data = Classify::where('id', $classifyId)->where('status', 1)->first();
        $info = !empty($data) ? $data->toArray() : array();
        //查询负责人信息
        if(!empty($info['leader'])){
            $userinfo = User::whereIn('uid', explode(',', $info['leader']))->get();
            $info['user'] = !empty($userinfo) ? $userinfo->toArray() : array();
        }
        
        return view('category.detail', ['info'=>$info]);
    }
    /**
     * 获取子分类详情
     */
    public function getDetailSub(){
        
        $classifyId = Input::get('subClassifyId');
        $data = Classify::where('id', $classifyId)->where('status', 1)->first();
        $info = !empty($data) ? $data->toArray() : array();
        //查询负责人信息
        if(!empty($info['leader'])){
            $userinfo = User::whereIn('uid', explode(',', $info['leader']))->get();
            $info['user'] = !empty($userinfo) ? $userinfo->toArray() : array();
        }
        //查询所有分类
        if(!empty($info)){
            $all = Classify::where('id',$info['pid'])->where('status', 1)->first();
            $info['classify'] = !empty($all) ? $all->toArray() : array();
        }
        
        return view('category.detailSub', ['info'=>$info]);
    }
}
