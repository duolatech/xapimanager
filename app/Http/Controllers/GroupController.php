<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\AuthGroup;
use App\Models\AuthRule;
use Input;

class GroupController extends Controller
{
    protected $startStatus = 1;
    protected $stopStatus = 2;
    protected $delStatus = 3;
    public function index()
    {
        $group = AuthGroup::get();
        $group = !empty($group) ? $group->toArray() : array();
        
        return view('group.index', ['group'=>$group]);
    }
    /**
     * 组节点
     */
    public function addGroup()
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
        
        return view('group.add', ['data'=>$data]);
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
        $group->rules = !empty($data['rules']) ? implode(',', $data['rules']) : '';
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
}
