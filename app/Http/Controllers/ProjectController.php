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
use DB;
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
        //切换到新创建的项目
        $proid = $pro->id;
        if(!empty($proid)){
            $uid = session::get('uid');
            $data = ProjectToggle::where('uid', $uid)->first();
            if(!empty($data->id)){
                $toggle = ProjectToggle::where('uid', $uid)->update(['proid'=>$proid]);
            }else{
                $toggle = ProjectToggle::insert(['uid'=>$uid, 'proid'=>$proid]);
            }
        }

        if(!empty($info)){
            //清除所有缓存
            Cache::flush();
            return response()->json(['status'=>200, 'message'=>'保存成功，2s后将跳转该项目的系统环境设置页']);
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

        //检查项目切换表中是否存在envid字段
        $sql = "SELECT * FROM information_schema.columns WHERE table_name = 'mx_project_toggle' AND column_name = 'status'";
        $arr = DB::select($sql);
        if(empty($arr)){
            $addSql = "ALTER table mx_project_toggle add `status` tinyint DEFAULT 0 COMMENT '状态：1当前项目，0非当前项目'";
            DB::statement($addSql);
        }
        if(in_array($proid, $proids)){
            $uid = session::get('uid');
            $data = ProjectToggle::where(array('uid'=>$uid, 'proid'=>$proid))->first();
            ProjectToggle::where(array('uid'=>$uid))->update(['status'=>0]);
            if(!empty($data->id)){
                $info = ProjectToggle::where(array('uid'=>$uid, 'proid'=>$proid))->update(['status'=>1]);
            }else{
                $apienv = ApiEnv::where(['proid'=>$proid,'status'=>1])->orderBy('id','asc')->first();
                $apienv = !empty($apienv) ? $apienv->toArray() : array();
                $envid = !empty($apienv['id']) ? $apienv['id'] : 0;

                $info = ProjectToggle::insert([
                    'uid'=>$uid, 
                    'proid'=>$proid, 
                    'envid'=>$envid, 
                    'status'=>1]
                );
            }
        }
        //清除所有缓存
        Cache::flush();
        if($info!==false){
            $envid = ApiEnv::where(['proid'=>$proid])->orderBy('id','asc')->limit(1)->value('id');
            $data = array('envid'=>$envid);
            return response()->json(['status'=>200, 'message'=>'项目切换成功，2s后将跳转到控制台', 'data'=>$data]);
        }else{
            return response()->json(['status'=>2010, 'message'=>'切换失败，请稍后重试']);
        }
    }
    /**
     * 环境切换
     */
    public function envToggle(Request $request){
        
        //当前项目id
        if(!empty($request['sys']['Project']['proid'])){
            $proid = $request['sys']['Project']['proid'];
        }else{
            $proid = 0;
        }
        //获取环境id
        $envid = Input::get('envid');
        //检查项目切换表中是否存在envid字段
        $sql = "SELECT * FROM information_schema.columns WHERE table_name = 'mx_project_toggle' AND column_name = 'envid'";
        $arr = DB::select($sql);
        if(empty($arr)){
            $addSql = "ALTER table mx_project_toggle add `envid` tinyint DEFAULT 0 COMMENT '当前环境id'";
            DB::statement($addSql);
        }
        $uid = session::get('uid');
        $info = ProjectToggle::where(array('uid'=>$uid, 'proid'=>$proid))->update(['envid'=>$envid]);
        //清除所有缓存
        Cache::flush();
        if($info!==false){
            return response()->json(['status'=>200, 'message'=>'环境切换成功']);
        }else{
            return response()->json(['status'=>2010, 'message'=>'切换失败，请稍后重试']);
        }

    }
}
