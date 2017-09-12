<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\AuthGroup;
use App\Models\AuthRule;
use App\Models\AuthData;
use App\Models\Project;
use App\Models\Classify;
use App\Models\User;
use App\Models\Company;
use Input;

class GroupController extends Controller
{
    const CHCHETIME = 5*60;
    protected $startStatus = 1;
    protected $stopStatus = 2;
    protected $delStatus = 3;
    protected $cls;// 接口分类
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
    public function index()
    {
        $group = AuthGroup::get();
        $group = !empty($group) ? $group->toArray() : array();
        
        return view('group.index', ['group'=>$group]);
    }
    /**
     * 获取权限组
     */
    public function ajaxGroup(){
        
        $keyword = Input::get('keyword');
        $group = AuthGroup::where('status',1);
        if(!empty($keyword)){
            $group = $group->where('groupname','like',"%{$keyword}%")->get();
        }else{
            $group = $group->get();
        }
        $group = !empty($group) ? $group->toArray() : array();
        $result = array(
            'status'    => 200,
            'message'   => '成功',
            'data'  => $group
        );
        return response()->json($result);
    }
    /**
     * 组节点
     */
    public function groupInfo()
    {
        $gid = Input::get('gid');
        //组拥有节点
        $info = array();
        if(!empty($gid)){
            $info = AuthGroup::where('id', $gid)->first();
            $info = !empty($info) ? $info->toArray() : array();
        }
        //所有节点
        $node = $this->getAllnode(0);
        
        $data = array(
            'group'  => $info,
            'node'  => $node
        );
        
        return view('group.info', ['data'=>$data]);
    }
    /**
     * 递归获取所有节点
     * @param $pid 父级id
     */
    public function getAllnode($pid){
    
        $info = AuthRule::where('pid', $pid)->where('isdel',2)
        ->orderBy('sort')->get();
        $info = !empty($info) ? $info->toArray() : array();
        foreach ($info as &$value){
            $value['child']=self::getAllnode($value['id']);
        }
        return $info;
    }
    /**
     * 组节点存储
     */
    public function groupStore(){
        
        $data = $_POST;
        if(empty($data['groupname'])){
            return response()->json([
                'status'=>4010,
                'message'=>'组名称不能为空',
            ]);
        }
        $gid = $data['gid'];
        $groupname = $data['groupname'];
        $groupinfo = AuthGroup::where('groupname', $groupname);
        if(!empty($gid)){
            $info = $groupinfo->where('id','<>', $gid)->first();
        }else{
            $info = $groupinfo->first();
        }
        if(!empty($info)){
            return response()->json(['status'=>4012, 'message'=>'用户组名已存在']);
        }
        $group = AuthGroup::find($gid);
        if(empty($group)){
            $group = new AuthGroup;
        }
        if(!empty($gid)){
            $group->id = $gid;
        }
        $group->groupname = $groupname;
        $group->status = (!empty($data['status']) && $data['status'] == 'on') ? 1 : 2;
        $group->description = $data['description'];
        $group->save();
        
        if(!empty($group->id)){
            return response()->json(['status'=>200, 'message'=>'组权限编辑成功']);
        }else{
            return response()->json(['status'=>4011, 'message'=>'组权限编辑失败']);
        }
        
    }
    /**
     * 用户组操作
     */
    public function operate(){
        
        $type = Input::get('type');
        $gid = Input::get('gid');
        
        switch ($type){
            case $this->startStatus:
            case $this->stopStatus:
                $data = $this->groupStatus($gid, $type);
                break;
            case $this->delStatus:
                $data = $this->delGroup($gid);
                break;
            default:
                $data = array(
                    'status'    => '4014',
                    'message'   => '操作类型错误'
                );
                break;
        }
        return response()->json($data);
        
    }
    /**
     * 启动/禁用用户组
     * @param $gid  组id
     * @param $type 组状态
     * @return $data 操作信息
     */
    public function groupStatus($gid, $type){
        
        $group = AuthGroup::find($gid);
        if(!empty($group)){
            $group->status = $type;
            $group->save();
        }
        if(!empty($group->id)){
            //清空可用菜单
            Cache::forget('ValidMenu');
            $data = array(
                'status'    => '200',
                'message'   => '操作成功'
            );
        }else{
            $data = array(
                'status'    => '4011',
                'message'   => '操作失败'
            );
        }
        return $data;
        
    }
    /**
     * 删除用户组
     */
    public function delGroup($gid){
        
        $group = AuthGroup::find($gid);
        if(!empty($group) && $gid!==1){
            $groupUser = AuthGroup::find($gid)->getGroupUser->first();
            $user = !empty($groupUser) ? $groupUser->toArray() : array();
            if(!empty($user)){
                $data = array(
                    'status'    => '4012',
                    'message'   => '该用户组有关联的用户，不能删除'
                );
            }else{
                $id = $group->delete();
                if(!empty($group->id)){
                    $data = array(
                        'status'    => '200',
                        'message'   => '删除成功'
                    );
                }
            }
        }
        if(empty($data)){
            $data = array(
                'status'    => '4013',
                'message'   => '删除失败，请稍后重试'
            );
        }
        return $data;
    }
    /**
     * 功能权限
     */
    public function featureAuth(Request $request){
        
        $id = Input::get('id');
        if(!empty($id)){
            $info = AuthGroup::where(['id'=>$id])->first();
        }
        $info = !empty($info) ? $info->toArray() : array();
        $info['rules'] = !empty($info['rules']) ? explode(',', $info['rules']) : array();
        $info['operate'] = !empty($info['operate']) ? explode(',', $info['operate']) : array();
        
        return view('group.feature', ['info'=>$info]);
    }
    /**
     * 功能权限保存
     */
    public function featureStore(){
        
        $gid = Input::get('id');
        $rules = Input::get('rules');
        $operate = Input::get('operate');
        if(!empty($gid)){
            $upt = AuthGroup::where(['id'=>$gid])->update([
                'rules'=>implode(',', $rules),
                'operate' => implode(',', $operate)
            ]);
            $data = array(
                'status'    => '200',
                'message'   => '保存成功'
            );
        }else{
            $data = array(
                'status'    => '4010',
                'message'   => '权限组不存在'
            );
        }
        return response()->json($data);
    }
    /**
     * 数据权限
     * 类型(1项目选择,2分类选择,3用户选择,4企业密钥)
     */
    public function dataAuth(Request $request){
        
        //项目信息
        $project = Project::where(['status'=>1])->get();
        $data['project'] = !empty($project) ? $project->toArray() : array();
        //接口分类
        $data['cateNum'] = array(
            'father' => Classify::where(['status'=>1, 'pid'=>0])->count(),
            'son'   => Classify::where(['status'=>1])->where('pid', '<>', 0)->count()
        );
        //用户数
        $data['userNum'] = User::count();
        //公司密钥
        $data['secret'] = Company::whereIn('status',[1,2])->count();
        //权限组
        $data['gid'] = Input::get('id');
        
        return view('group.data', ['data'=>$data]);
    }
    /**
     * 获取数据范围
     * 类型(1项目选择,2分类选择,3用户选择,4企业密钥)
     */
    public function ajaxDataRange(){
        
        $type = Input::get('type');
        $gid = Input::get('gid');
        $info = array();
        
        $record = AuthData::where(['groupid'=>$gid, 'type'=>$type])->value('record');
        
        switch ($type){
            case '1':     //项目选择
                $info = $this->getProjectInfo($gid, $record);
                break;
            case '2':     //分类选择
                $info = $this->getClassifyInfo($gid, $record);
                break;
            case '3':     //用户选择
                $info = $this->getUserInfo($gid, $record);
                break;
            case '4':     //公司密钥
                $info = $this->getSecretInfo($gid, $record);
                break;
        }
        $result = array(
            'status' => 200,
            'data'=> $info
        );
        return response()->json($result);
    }
    /**
     * 获取项目选择信息
     */
    public function getProjectInfo($gid, $record){
        
        $record = !empty($record) ? explode(',', $record) : array();
        $project = Project::where(['status'=>1])->get();
        
        $field = array(
            'proname' => '项目名',
            'attribute' => '属性', 
            'status' => '状态'
        );
        $data = array();
        foreach ($project as $value){
            $pro = array(
                'id'    => $value['id'],
                'proname' => $value['proname'],
                'attribute' => ($value['attribute']==1) ? '所有权限组' : '仅指定权限组',
                'status' => ($value['status']==1) ? '<span class="label bg-success">正常</span>' : '<span class="label bg-light">弃用</span>',
                'selected' => in_array($value['id'], $record) ? 1 : 2 //1被选中 2未被选中
            );
            $pro['fieldValue'] = array($pro['proname'], $pro['attribute'], $pro['status']);  
            $data[] = $pro;
        }
        $result = array(
            'type' => '1',
            'field' => $field,
            'data'  => $data,
            'subordinate' => array(
                'project'   => array(),  //从属项目显示(空不显示,非空显示)
                'classify'  => array(),  //从属分类显示(空不显示,非空显示)
            )
        );
        
        return $result;
    }
    /**
     * 获取分类选择
     * 分类record为三级数组结构，一级为项目，二级为父级分类，三级为子分类
     * @param $gid 组id
     */
    public function getClassifyInfo($gid, $record){
        
        $record = !empty($record) ? json_decode($record, true) : array();
        $field = array(
            'apiname' => '资源名',
            'version' => '版本',
            'URI' => 'URI',
            'author' => '维护人',
            'status' => '状态'
        );
        //查询所属项目
        $proidA = AuthData::where(['groupid'=>$gid, 'type'=>1])->value('record');
        $proids = !empty($proidA) ? explode(',', $proidA) : array();
        $project = Project::where(['attribute'=>1,'status'=>1]);
        if(!empty($proids) && is_array($proids)){
                $project = $project->orWhereIn('id', $proids);
        }
        dump($record);
        $project = $project->get();
        $project = !empty($project) ? $project->toArray() : array();
        //分类信息
        $classify = array();
        foreach($project as $key=>&$pro){
            $pro['selected'] = ($key==0) ? 1 :0;
            if(!empty($pro['id'])){
                $proid = $pro['id'];
                $classify['allClassify'] = Classify::getClassify($proid, 0);
                //已选中分类
                if(!empty($record[$proid])){
                    foreach ($record[$proid] as $key=>$value){
                        $classify['classifyIds'][] = $key;
                        foreach ($value as $ko=>$vol){
                            $classify['classifyIds'][] = $ko;
                        }
                    }
                }
            }
            $pro['classify'] = $classify;
        }
        $item = array();
        foreach ($project as $ko=>$vol){
            $item[$vol['id']] = $vol;
        }
        $result = array(
            'type' => '2',
            'field' => $field,
            'data'  => array(),
            'subordinate' => array(
                'project'   => $item,  //从属项目显示(空不显示,非空显示)
                'classify'  => [1], //从属分类显示(空不显示,非空显示)
                ''
            )
        );
        return $result;
    }
    /**
     * 获取用户选择
     * @param $gid 组id
     */
    public function getUserInfo($gid, $record){
        
        $record = !empty($record) ? explode(',', $record) : array();
        $user = User::select('uid','username','email','phone','status')->get()->toArray();
        $field = array(
            'username' => '用户名',
            'email' => '邮箱', 
            'phone' => '手机号',
            'status' => '状态'
        );
        $data = array();
        foreach($user as &$value){
            $value['id'] = $value['uid'];
            $value['status'] = ($value['status'] == 1) ? '<span class="label bg-success">在职</span>' : '<span class="label bg-light">离职</span>';
            $value['selected'] = in_array($value['uid'], $record) ? 1 : 2; //1被选中 2未被选中
            $value['fieldValue'] = array($value['username'], $value['email'], $value['phone'], $value['status']);
        }
        
        $result = array(
            'type' => '3',
            'field' => $field,
            'data'  => $user,
            'subordinate' => array(
                'project'   => array(),  //从属项目显示(空不显示,非空显示)
                'classify'  => array(),  //从属分类显示(空不显示,非空显示)
            )
        );
        
        return $result;
    }
    /**
     * 获取公司密钥
     * @param $gid 组id
     */
    public function getSecretInfo($gid, $record){
        
        $record = !empty($record) ? json_decode($record, true) : array();
        $field = array(
            'company' => '公司名',
            'appId' => 'appId',
            'appSecret' => 'appSecret',
            'status' => '状态'
        );
        //查询所属项目
        $proidA = AuthData::where(['groupid'=>$gid, 'type'=>1])->value('record');
        $proids = !empty($proidA) ? explode(',', $proidA) : array();
        $project = Project::where(['attribute'=>1,'status'=>1]);
        if(!empty($proids) && is_array($proids)){
            $project = $project->orWhereIn('id', $proids);
        }
        $project = $project->get();
        $cpy = array();
        $data = array();
        $project = !empty($project) ? $project->toArray() : array();
        foreach($project as $key=>&$pro){
            $pro['selected'] = 0;
            if($key==0){
                $pro['selected'] = 1;
                $cpy = Company::where(['proid'=> $pro['id']])->whereIn('status',[1,2])->get();
                $cpy = !empty($cpy) ? $cpy->toArray() : array();
                foreach($cpy as &$value){
                    $value['status'] = ($value['status'] == 1) ? '<span class="label bg-success">正常</span>' : '<span class="label bg-warning">冻结</span>';
                    $value['selected'] = !empty($record[$pro['id']]) && in_array($value['id'], $record[$pro['id']]) ? 1 : 2; //1被选中 2未被选中
                    $value['fieldValue'] = array($value['company'], $value['appId'], $value['appSecret'], $value['status']);
                }
            }
        }
        $result = array(
            'type' => '4',
            'field' => $field,
            'data'  => $cpy,
            'subordinate' => array(
                'project'   => $project,  //从属项目显示(空不显示,非空显示)
                'classify'  => array(),  //从属分类显示(空不显示,非空显示)
            )
        );
        return $result;
    }
    /**
     * 数据权限保存
     * 类型(1项目选择,2分类选择,3用户选择,4企业密钥)
     */
    public function dataStore(){
        
        $post = $_POST;
        $gid = $post['gid'];
        $type = $post['auth_type'];
        if(!empty($type)){
            $data = array();
            $post['range'] = !empty($post['range']) ? $post['range'] : array();
            switch ($type){
                case 1:
                    $data = implode(',', $post['range']);
                    break;
                case 2:
                    if(!empty($post['proid']) && !empty($post['classify']) && !empty($post['subClassify'])){
                        $record = AuthData::where(['groupid'=>$gid, 'type'=>$type])->value('record');
                        $record = !empty($record) ? json_decode($record, true) : array();
                        
                        foreach ($post['subClassify'] as $key=>$value){
                            foreach ($value as $ka=>$val){
                                if(in_array($key, $post['classify'])){
                                    if($val==$post['twoClassify']){
                                        $record[$key][$ka] = !empty($record[$key][$ka]) ? $record[$key][$ka] : array();
                                        $data[$post['proid']][$key][$val] = array_merge($record[$key][$ka], $post['range']);
                                    }else{
                                        $data[$post['proid']][$key][$val] = array();
                                    }
                                }
                            }
                        }
                        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                    }
                    break;
                case 3:
                    $data = implode(',', $post['range']);
                    break;
                case 4:
                    if(!empty($post['proid'])){
                        $data = array(
                            $post['proid'] => $post['range']
                        );
                        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                    }
                    break;
            }
            //插入数据库
            $auth = AuthData::where(['groupid'=>$gid, 'type'=>$type]);
            $count = $auth->count();
            if($count>0){
                $id = $auth->update(['record'=>$data]);
            }else{
                $id = AuthData::insert(
                    [
                        'groupid'=>$gid,
                        'type'=>$type,
                        'record'=>$data,
                        'ctime'=>time()
                    ]
                );
            }
            return response()->json(array('status'=>200, 'message'=>'保存成功'));
        }
    }
    
    
}
