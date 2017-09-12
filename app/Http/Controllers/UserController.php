<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Input, Validator, Session, Log;
use App\Models\AuthGroup;
use App\Models\AuthAccess;
use App\Models\User;
use App\tools\FileUtil;

class UserController extends Controller
{
    
    protected $limit = 20;
    /**
     * 用户列表
     * @param Request $request
     */
    public function index(Request $request){
        
        $group = AuthGroup::where(['status'=>1])->get();
        $list['group'] = !empty($group) ? $group->toArray() : array();
        
        return view('user.index', ['list'=>$list]);
       
    }
    /**
     * ajax 获取用户数据
     */
    public function ajaxUser(){
        
        $page = Input::get('page');
        $search = Input::get('search');
        
        $page = !empty($page) ? $page : 1;
        $start = ($page - 1) * ($this->limit);
        $list['info'] = array();
        $User = new User();
        $field = Input::get('field');
        $keyword = Input::get('keyword');
        $groupId = Input::get('group_id');
        //查询用户列表
        $list = $User->getUser($field, $keyword, $groupId, $start, $this->limit);
        //查询用户组
        $authGroup = AuthGroup::get();
        $authGroup = !empty($authGroup) ? $authGroup->toArray() : array();
        foreach ($authGroup as $value){
            $group[$value['id']] = $value['groupname'];
        }
        foreach ($list['info'] as &$value){
            $value['sex'] = $value['sex'] == 1 ? '男' : '女';
            $value['ctime'] = date('Y-m-d H:i', $value['ctime']);
            $value['groupname'] = !empty($group[$value['group_id']]) ? $group[$value['group_id']] : '';
        }
        //页面总数
        $list['pageCount'] = ceil($list['totalCount']/$this->limit);
        return response()->json(['status'=>200, 'data'=>$list]);
    }
    /**
     * 编辑用户页面
     */
    public function userInfo(Request $request)
    {
        $uid = intval(Input::get('uid'));
        $info = array();
        if(!empty($uid)){
            $user = User::where('uid',$uid)->first();
            $info['user'] = !empty($user) ? $user->toArray() : array();
            $userGroup  = AuthAccess::find($uid);
            $info['userGroup'] = !empty($userGroup) ? $userGroup->toArray() : array();
        }
        //查询可用的用户组
        $authGroup = AuthGroup::get();
        $authGroup = !empty($authGroup) ? $authGroup->toArray() : array();
        $group = array();
        foreach ($authGroup as $value){
            if($value['status']==1){
                $group[$value['id']] = $value['groupname'];
            }
        }
        $info['group'] = $group;
        
        return view('user.info',['info'=>$info]);
    }
    /**
     * 用户详情页
     */
    public function detail(){
        $uid = intval(Input::get('uid'));
        $info = array();
        if(!empty($uid)){
            $user = User::where('uid',$uid)->first();
            $info['user'] = !empty($user) ? $user->toArray() : array();
            $userGroup  = AuthAccess::find($uid);
            $info['userGroup'] = !empty($userGroup) ? $userGroup->toArray() : array();
        }
        //查询可用的用户组
        $groupId=!empty($info['userGroup']['group_id']) ? $info['userGroup']['group_id'] : 0;
        $info['user']['groupname'] = AuthGroup::where(['id'=>$groupId])->value('groupname');
        if(!empty($info['user']['status'])){
            $status = $info['user']['status'];
            if($status==1){
                $info['user']['userStatus'] = '在职';
            }elseif($status==2){
                $info['user']['userStatus'] = '离职';
            }elseif($status==3){
                $info['user']['userStatus'] = '待激活';
            }
        }
        
        return view('user.detail',['info'=>$info]);
    }
    /**
     * 保存用户
     */
    public function userStore(userRequest $request){
        
        $data = Input::all();
        $Field = array('username', 'group_id', 'password', 'phone', 
            'email', 'status_id', 'userid', 'avatar');
        foreach ($Field as $value){
            if(array_key_exists($value, $data)){
                $post[$value] = trim($data[$value]);
            }
        }
        $uid = intval($post['userid']);
        $User = new User();
        $param = array(
            'username'  => $post['username'],
            'phone' => $post['phone'],
            'email' => $post['email']
        );
        //检查username、phone、email是否重复
        $info = $User->isRepeat($uid, $param);
        $repeat = array();
        $fields = array(
            'username'=>'用户名',
            'phone' => '手机号',
            'email' => 'E-mail'
        );
        if(!empty($info)){
            foreach ($fields as $key=>$value){
                if($info[$key]==$param[$key]){
                    $repeat[] = $value.': '.$param[$key].'已存在';
                }
            }
        }
        
        if(empty($repeat)){
            //保存或更新用户数据
            $userId = $this->saveUser($uid, $post);
            //保存或更新用户组
            $this->saveGroup($userId, $post);
            
            return response()->json(['status'=>200, 'message'=>'保存成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>implode('<br/>', $repeat)]);
        }
        
        
    }
    /**
     * 保存用户信息
     * @param $uid  用户id
     * @param $post 用户数据
     * @return 用户id
     */
    public function saveUser($uid, $post){
        
        if(!empty($uid)){
            $user = User::find($uid);
            $user->uid = $uid;
        }
        if(empty($user)){
            $user = new User();
            $user->ctime = time();
        }
        $user->username = $post['username'];
        if(trim($post['password'])!='security'){
            $user->salt = GetRandStr(6);
            $user->password = md5(md5($post['password']).$user->salt);
        }
        $user->phone = $post['phone'];
        $user->email = $post['email'];
        $user->avatar = $post['avatar'];
        $user->status = $post['status_id'];
        $user->save();
        
        return $user->uid;
    }
    /**
     * 保存用户组
     * @param $uid  用户id
     * @param $post 用户数据
     */
    public function saveGroup($uid, $post){
        
        $authAccess = AuthAccess::find($uid);
        if(empty($authAccess)){
            $authAccess = new AuthAccess();
        }
        $authAccess->uid =  $uid;
        $authAccess->group_id = $post['group_id'];
        $authAccess->save();
        
    }
}
