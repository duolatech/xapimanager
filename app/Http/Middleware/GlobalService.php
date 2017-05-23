<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\AuthAccess;
use App\Models\AuthGroup;
use App\Models\AuthRule;
use App\Models\Site;
use App\Models\ApiEnv;
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
        $request['sys'] = $this->getSysInfo();
        
        //页面共享全局服务信息
        view()->share('sys', $request['sys']);
        
        return $next($request);
    }
    /**
     * 获取全局基本信息
     */
    public function getSysInfo(){
    
        $notNeedLogin = array('Website', 'ValidOperate', 'ApiEnv', 'AllNode');
        $needLogin = array('AuthRule','ValidMenu', 'Router');
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
     */
    public function getValidMenu(){
    
       if(empty($this->service['rules'])){
            $auth = $this->getAuthRule();
       }else{
            $auth = $this->service['rules'];
       }
       
       $menu = self::getMenu(0, $auth['rules']);
       $this->service['menu'] = $menu;
       
       return $menu;
    }
    /**
     * 递归查询所有显示菜单
     * @param $pid 父级id
     * @param $auth 用户所在组权限
     * @return 可用菜单
     */
    public function getMenu($pid, $auth){
    
        $info = AuthRule::where('pid',$pid)
        ->where('status',1)->where('type',1)->where('isdel',2)->whereIn('id', $auth)
        ->orderBy('sort')->get();
        $info = !empty($info) ? $info->toArray() : array();
        foreach ($info as &$value){
            $value['path'] = !empty($value['path']) ? '/'.$value['path'] : '#';
    
        }
        foreach ($info as &$value){
            $value['child']=self::getMenu($value['id'], $auth);
        }
        return $info;
    }
    /**
     * 查询可用操作
     * @return 可操作列表
     */
    public function getValidOperate(){
    
        $opt = cache::get('ValidOperate');
        if(empty($opt)){
            $info = AuthRule::where('type', 2)->where('status',1)->get();
            $info = !empty($info) ? $info->toArray() : array();
            $opt = array();
            foreach($info as $value){
                $opt[$value['id']] = $value['path'];
            }
            Cache::put('ValidOperate', $opt, self::CHCHETIME);
        }
    
        return $opt;
    }
    /**
     * 获取所有路由
     * @return 路由id
     */
    public function getRouter(){
    
        $info = AuthRule::where('type',1)->where('isdel',2)->get();
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
            $apienv = ApiEnv::where('status', 1)->get();
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
            $info = AuthRule::where('isdel',2)->get();
            $info = !empty($info) ? $info->toArray() : array();
            $node = array();
            foreach($info as $value){
                $node[$value['id']] = $value;
            }
            Cache::put('allNode', $node, self::CHCHETIME);
            //更新超级管理员权限节点
            if(!empty($node) && is_array($node)){
                $rules = implode(',', array_keys($node));
                AuthGroup::where('id', 1)->update(array('rules'=>$rules));
            }
        }
        return $node;
    }
    
}
