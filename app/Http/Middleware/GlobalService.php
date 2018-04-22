<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\AuthAccess;
use App\Models\AuthGroup;
use App\Models\AuthOperate;
use App\Models\AuthRule;
use App\Models\Site;
use App\Models\ApiEnv;
use App\Models\Project;
use App\Models\ProjectToggle;
use App\Models\AuthData;
use App\Models\Message;
use App\Http\Controllers\ApiController;

class GlobalService
{
    /**
     * 缓存时间
     */
    const CHCHETIME = 24*60*60; 
    protected $uid, $service;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->uid = session::get('uid');
        
        //检查安装情况
        $isInstall = config('app.appInstall');
        $path = $request->path();
        if(empty($isInstall) && !preg_match('/Install/i', $path)){
            return redirect()->route('install.index');
        }
        //获取全局信息
        if(!empty($isInstall)){
            $request['sys'] = $this->getSysInfo();
        }else{
            $request['sys'] = array(
                'Website' => array(
                    'title' => '安装向导 - xApi Manager',
                    'keywords' => 'xApi Manager，哆啦接口管理平台',
                    'description' => 'XAPI MANAGER -专业实用的开源接口管理平台，为程序开发者提供一个灵活，方便，快捷的API管理工具，让API管理变的更加清晰、明朗',
                )
            );
        }
        //页面共享全局服务信息
        view()->share('sys', $request['sys']);
        
        return $next($request);
    }
    /**
     * 获取全局基本信息
     */
    public function getSysInfo(){
    
        $notNeedLogin = array('Website', 'ApiEnv', 'AllNode');
        $needLogin = array('AuthRule','ValidMenu', 'Router', 'Project', 'UnreadMessage', 'Operate');
        $allFields = array_merge($notNeedLogin, $needLogin);
        $result = array();
        foreach ($allFields as $value){
            
            $action = 'get'.$value;
            if(!in_array($value, $needLogin)){
                $result[$value] = $this->$action();
            }elseif(!empty($this->uid)){
                $result[$value] = $this->$action();
            }
        }
        return $result;
    
    }
    /**
     * 获取站点信息
     * @return 站点信息
     */
    public function getWebsite(){
    
        $site = cache::get('Website');
        if(empty($site)){
            $site = array();
            $website = Site::where('id','>=',1)->get();
            $website = !empty($website) ? $website->toArray() : array();
            foreach ($website as $vol){
                $site[$vol['key']] = htmlspecialchars($vol['value'],ENT_QUOTES);
            }
            Cache::put('Website', $site, self::CHCHETIME);
        }
    
        return $site;
    
    }
    /**
     * 获取可用菜单
     * @return \App\Http\Service\可用菜单
     * rules 中1,2,3为栏目(导航栏/项目选择/用户中心)，所有用户都拥有
     */
    public function getValidMenu(){
    
       if(empty($this->service['rules'])){
            $auth = $this->getAuthRule();
       }else{
            $auth = $this->service['rules'];
       }
       $rules = array_merge(array(1,2,3),$auth['rules']);
       $menu = self::getMenu(0, $rules);
       $newMenu = array();
       foreach ($menu as $value){
           $newMenu[$value['id']] = $value;
       }
       $this->service['menu'] = $newMenu;
       
       return $newMenu;
    }
    /**
     * 递归查询所有显示菜单
     * @param $pid 父级id
     * @param $rules 用户所在组权限节点
     * @return 可用菜单
     */
    public function getMenu($pid, $rules){
    
        $info = AuthRule::where('pid',$pid)
        ->where('isdel',2)->whereIn('id', $rules)
        ->orderBy('sort')->get();
        $info = !empty($info) ? $info->toArray() : array();
        foreach ($info as &$value){
            $value['path'] = !empty($value['path']) ? '/'.$value['path'] : '#';
        }
        foreach ($info as &$value){
            $value['child']=self::getMenu($value['id'], $rules);
        }
        
        return $info;
    }
    /**
     * 获取所有路由
     * @return 路由id
     */
    public function getRouter(){
    
        $info = AuthRule::where('isdel',2)->get();
        $info = !empty($info) ? $info->toArray() : array();
        foreach ($info as $value){
            $path = trim($value['path'], '/');
            if($value['pid']==0){
                $result[$path] = array(
                    'title' => $value['title'],
                    'id'   => $value['id']
                );
            }else{
                $result[$path] = array(
                    'title' => $value['title'],
                    'id'   => $value['pid']
                );
            }
        }
        return $result;
    }
    /**
     * 获取用户权限
     */
    public function getAuthRule(){
    
        $rule = array();
        if(!empty($this->uid)){
            $auth = AuthAccess::find($this->uid);
            if(empty($auth)){
                AuthAccess::insert(['uid'=>$this->uid, 'group_id'=>2]);
            }
            $rule = AuthAccess::find($this->uid)->getGroupRule->toArray();
            $rule['rules'] = explode(',', $rule['rules']);
        }
        $this->service['rules'] = $rule;
        return $rule;
    }
    /**
     * Api环境
     */
    public function getApiEnv(){
        
        $apienv = cache::get('apienv');
        if(empty($apienv)){
            $proid = ProjectToggle::where(['uid'=>$this->uid])->value('proid');
            $apienv = ApiEnv::where(['proid'=>$proid,'status'=>1])->get();
            $apienv = !empty($apienv) ? $apienv->toArray() : array();
            Cache::put('apienv', $apienv, self::CHCHETIME);
        }
        return $apienv;
    }
    /**
     * 获取所有可操作节点,更新超级管理员权限节点
     */
    public function getAllNode(){
        
        $node = cache::get('allNode');
        if(empty($node)){
            //超级管理员自动获取所有菜单节点
            $info = AuthRule::where('isdel',2)->get();
            $info = !empty($info) ? $info->toArray() : array();
            $rulenode = array();
            foreach($info as $value){
                $rulenode[$value['id']] = $value;
            }
            Cache::put('allNode', $rulenode, self::CHCHETIME);
            $rules = implode(',', array_keys($rulenode));
            //超级管理员自动获取所有菜单节点的可操作节点
            $opt = AuthOperate::where('status',1)->get();
            $opt = !empty($opt) ? $opt->toArray() : array();
            $optnode = array();
            foreach($info as $value){
                $optnode[$value['id']] = $value;
            }
            $operate = implode(',', array_keys($optnode));
            AuthGroup::where('id', 1)->update(array(
                'rules'=>$rules,
                'operate'=>$operate
            ));
        }
        return $node;
    }
    /**
     * 获取用户权限组项目
     */
    public function getProject(){
        
        $sysProject = cache::get('sysProject');
        if(empty($sysProject)){
            //查询用户组所属项目
            $dataRule = AuthAccess::find($this->uid)->getGroupProject()->where('type',1)->get();
            $dataRule = !empty($dataRule) ? $dataRule->toArray() : array();
            
            $proids = array();
            foreach ($dataRule as $rule){
                $proids[] = $rule['record'];
            }
            $project = Project::where(['attribute'=>1,'status'=>1]);
            if(!empty($proids) && is_array($proids)){
                $project = $project->orWhereIn('id', $proids);
            }
            $project = $project->get();
            $project = !empty($project) ? $project->toArray() : array();
            //查询用户当前选择项目
            $sysProject = array();
            $pid = ProjectToggle::where(['uid'=>$this->uid])->value('proid');
            foreach($project as $value){
                $value['active'] = ($value['id']==$pid) ? 1 :0;
                $sysProject['info'][$value['id']] = $value;
                $sysProject['proid'] = $pid;
            }
            Cache::put('sysProject', $sysProject, self::CHCHETIME);
        }
        return $sysProject;
    }
    /**
     * 获取用户未读消息数
     */
    public function getUnreadMessage(){
        
        $infoNum = Message::where(['receiver'=>$this->uid,'isread'=>2])->count();
        
        return $infoNum;
    }
    /**
     * 页面操作
     */
    public function getOperate(){
        
        $operation = cache::get('operation');
        if(empty($operation)){
            $info = AuthOperate::where(['status'=>1])->get();
            $info = !empty($info) ? $info->toArray() : array();
            $opt = array();
            foreach ($info as $value){
                $opt[$value['rid']][] = array(
                    'id'=> $value['id'],
                    'title'=>$value['title'],
                    'path'=>$value['path']
                );
            }
            Cache::put('operation', $opt, self::CHCHETIME);
        }
        
        return $operation;
    }
    
}
