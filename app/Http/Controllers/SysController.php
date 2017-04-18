<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\siteRequest;
use App\Http\Requests\menuRequest;
use App\Http\Service\BaseService;
use App\Models\Site;
use App\Models\AuthRule;
use App\Models\ApiEnv;
use Input, Session, Log;

class SysController extends Controller
{
    /**
     * 网站设置
     */
    public function site(){
		
        return view('sys.site');
		
    }
    /**
     * 新增菜单
     */
    public function addMenu(Request $request){
        
        $validMenu = $request['sys']['ValidMenu'];
        $id = Input::get('id');
        $menu = array();
        if(!empty($id)){
            foreach ($validMenu as $value){
                $menu[$value['id']] = GetFilterArray($value, array('child'));
                foreach ($value['child'] as $vol){
                    $menu[$vol['id']] = GetFilterArray($vol, array('child'));
                }
            }
        }
        $current_menu = !empty($menu[$id]) ? $menu[$id] : array();
        
        return view('sys.addmenu', ['cmenu'=>$current_menu]);
    }
    /**
     * 后台菜单
     */
    public function menu(){
        
        return view('sys.menu');
    }
    /**
     * 站点信息保存
     * @param siteRequest $request
     */
    public function siteStore(siteRequest $request){
        
        
        $data = array();
        $post = Input::all();
        $Field = array('sitename', 'title', 'keywords', 'description', 'copyright');
        foreach ($Field as $value){
            if(array_key_exists($value, $post)){
                $data[$value] = trim($post[$value]);
            }
        }
        foreach ($data as $key=>$value){
            Site::where('key', $key)->update(['value'=>$value]);
        }
        Cache::forget('Website');
        return response()->json(['status'=>200, 'message'=>'保存成功']);
    }
    /**
     * 保存菜单
     */
    public function menuStore(menuRequest $request){
        
        $post = Input::all();
        if(!empty($post['id'])){
            $menu  = AuthRule::find($post['id']);
        }
        if(empty($menu)){
            $menu = new AuthRule;
        }
        //获取数据
        $menu->pid = $post['pid'];
        $menu->title = $post['title'];
        $menu->path = trim($post['path'], '/');
        $menu->icon = $post['icon'];
        $menu->sort = $post['sort'];
        $menu->status = ($post['status']=='true') ? 1 : 0;
        //保存
        $info = $menu->save();
        if(!empty($info)){
            Cache::forget('ValidMenu');
            Cache::forget('allNode');
            return response()->json(['status'=>200, 'message'=>'保存成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'保存失败，请稍后重试！']);
        }
    }
    /**
     * 批量删除菜单
     */
    public function delMenu(){
        
        $post = Input::all();
        $mids = explode('#', $post['mids']);
        foreach ($mids as $value){
            $ids[] = intval($value);
        }
        if(!empty($ids) && is_array($ids)){
            $info = AuthRule::whereIn('id', $ids)->update(['isdel'=>1]);
        }
        if(!empty($info)){
            Cache::forget('ValidMenu');
            return response()->json(['status'=>200, 'message'=>'删除成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'删除失败，请稍后重试！']);
        }
    }
    /**
     * Api环境选择
     */
    public function sysenv(){
        
        $apienv = ApiEnv::get();
        $apienv = !empty($apienv) ? $apienv->toArray() : array();
        return view('sys.env', ['data'=>$apienv]);
    }
    /**
     * 更新Api环境
     */
    public function envStore(){
        
        $data = $_POST;
        
        $apienv = $data['apienv'];
        //环境名称
        foreach ($apienv['name'] as $name){
            $name = trim($name);
            if(empty($name)){
                return response()->json([
                    'status'=>4010,
                    'message'=>'Api环境名称不能为空'
                ]);
            }
        }
        //状态处理
        foreach ($apienv['id'] as $key=>$envid){
            if(!empty($apienv['status'][$envid]) && $apienv['status'][$envid]=='on'){
                $status[$key] = 1;
            }else{
                $status[$key] = 2;
            }
        }
        $data['apienv']['status'] = $status;
        //数据格式化
        $penv = fieldParamSort($data['apienv'], 'id');
        //更新数据
        $sysEnv = new ApiEnv();
        foreach($penv as $value){
            $arr = array(
                'status'    => $value['status'],
                'envname'   => trim($value['name'])
            );
            $info = $sysEnv->where('id', $value['id'])->update($arr);
        }
        //清除缓存
        Cache::forget('apienv');
        return response()->json([
            'status'=>200,
            'message'=>'更新成功'
        ]);
    }
}
