<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\projectRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\ProjectToggle;
use App\Models\User;
use App\Models\AuthData;
use App\Models\AuthGroup;
use App\Models\ApiEnv;
use Input;
class ProjectController extends Controller
{
    /**
     * 添加项目页
     */
    public function create(){
        
        return view('project.info');
    }
    /**
     * 编辑项目页
     */
    public function edit(){
    
        //查询项目信息
        $id = Input::get('id');
        $id = !empty($id) ? intval($id) : 0;
        $info = Project::where(['id'=>$id])->first();
        $info = !empty($info) ? $info->toArray() : array();
        //项目所属分组信息
        $aData = AuthData::where(['record'=>$id])->get();
        $aData = !empty($aData) ? $aData->toArray() : array();
        $gids = array();
        foreach ($aData as $value){
            $gids[] = $value['groupid'];
        }
        $groups = AuthGroup::whereIn('id',$gids)->get();
        $groups = !empty($groups) ? $groups->toArray() : array();
        foreach ($groups as $value){
            $info['groups'][$value['id']] = $value;
        }
        return view('project.info', ['data'=>$info]);
    }
    /**
     * 保存项目
     * @param projectRequest $request
     */
    public function store(projectRequest $request){
        
        $id = Input::get('id');
        if(!empty($id)){
            $pro  = Project::find($id);
            $pro->id = $id;
        }
        if(empty($pro)){
            $pro = new Project();
        }
        //获取数据
        $groups = Input::get('groups');
        $groupids = (!empty($groups) && is_array($groups)) ? array_unique($groups) : array(0);
        $pro->proname = Input::get('proname');
        $pro->attribute = Input::get('attribute');  //项目属性(1公有,2私有)
        $pro->desc = Input::get('desc');
        $pro->status = 1;
        $pro->ctime = time();
        //保存
        $info = $pro->save();
        //权限组保存
        $time = time();
        $record = $pro->id;
        AuthData::where('type',1)->where('record',$record)->delete();
        if($pro->attribute==2){
            foreach ($groupids as $id){
                $authData[] = array(
                    'groupid'   => $id,
                    'type'  => 1,
                    'record'    => $record,
                    'ctime' => $time
                );
            }
            $aData = AuthData::insert($authData);
        }
        if(!empty($info)){
            //清除所有缓存
            Cache::flush();
            return response()->json(['status'=>200, 'message'=>'保存成功，2s后将跳转到控制台']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'保存失败，请稍后重试！']);
        }
        
    }
    /**
     * 项目切换
     */
    public function toggle(Request $request){
        $proid = Input::get('proid');
        $sysProject = $request['sys']['Project']['info'];
        $proids = array();
        foreach($sysProject as $value){
            $proids[] = $value['id'];
        }
        $info = false;
        if(in_array($proid, $proids)){
            $uid = session::get('uid');
            $data = ProjectToggle::where('uid', $uid)->first();
            if(!empty($data->id)){
                $info = ProjectToggle::where('uid', $uid)->update(['proid'=>$proid]);
            }else{
                $info = ProjectToggle::insert(['uid'=>$uid, 'proid'=>$proid]);
            }
        }
        if($info!==false){
            //清除所有缓存
            Cache::flush();
            $envid = ApiEnv::where(['proid'=>$proid])->orderBy('id','asc')->limit(1)->value('id');
            $data = array('envid'=>$envid);
            return response()->json(['status'=>200, 'message'=>'切换成功，2s后将刷新本页面', 'data'=>$data]);
        }else{
            return response()->json(['status'=>2010, 'message'=>'切换失败，请稍后重试']);
        }
    }
}
