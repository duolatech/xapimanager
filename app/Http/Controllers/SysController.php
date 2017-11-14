<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\siteRequest;
use App\Http\Requests\menuRequest;
use App\Http\Requests\envRequest;
use App\Http\Service\BaseService;
use App\Models\Site;
use App\Models\AuthRule;
use App\Models\ApiEnv;
use App\Models\Project;
use App\Models\OperateLog;
use App\Models\User;
use App\Models\ProjectToggle;
use Input, Session, Log;
require_once app_path().'/tools/HttpClient/vendor/autoload.php';

class SysController extends Controller
{
    const CURLOPT_TIMEOUT = 30;
    protected $limit = 1;
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
     * 项目设置页面
     */
    public function project(){
        
        $project = Project::where(['status'=>1])->get();
        $project = !empty($project) ? $project->toArray() : array();
        
        return view('sys.project', ['data'=>$project]);
    }
    /**
     * Api环境选择
     */
    public function sysenv(){
        
        $apienv = ApiEnv::orderBy('status','asc')->get();
        $apienv = !empty($apienv) ? $apienv->toArray() : array();
        
        return view('sys.env', ['data'=>$apienv]);
    }
    /**
     * 更新Api环境
     */
    public function envStore(envRequest $request){
        
        $isopen = Input::get('isopen');
        $status = (!empty($isopen) && $isopen =='on') ? 1 : 2;
        $envid = Input::get('envid');
        $arr = array(
            'envname' => Input::get('envname'),
            'domain'  => Input::get('domain'),
            'status'  => $status
        );
        $info = ApiEnv::where('id', $envid)->update($arr);
        
        //清除缓存
        Cache::forget('apienv');
        return response()->json([
            'status'=>200,
            'message'=>'更新成功'
        ]);
    }
    /**
     * 日志页面
     */
    public function log(){
    
        return view('sys.log');
    }
    /**
     * 获取日志数据
     */
    public function ajaxLog(){
        
        $username = Input::get('username');
        $startDate = Input::get('startDate');
        $endDate = Input::get('endDate');
        $page = Input::get('page');
        $page = !empty($page) ? $page : 1;
        $start = ($page - 1) * ($this->limit);
        
        //获取用户id
        $uids = array();
        if(!empty($username)){
            $user = User::where('username', 'like', "%{$username}%")->get();
            $user = !empty($user) ? $user->toArray() : array();
            $uids = array();
            foreach ($user as $value){
                $uids[] = $value['uid'];
            }
        }
        //日志数据查询
        $info = OperateLog::leftJoin('project as p', 'log.project', '=', 'p.id')
                  ->leftJoin('user as u', 'log.operator','=', 'u.uid')
                  ->leftJoin('apienv as e', 'log.envid', '=', 'e.id')
                  ->select("log.id","p.proname","e.envname","u.username","log.desc","log.addtime");
        if(!empty($uids)){
            $info = $info->whereIn('log.operator',$uids);
        }
        if(!empty($startDate)){
            $info = $info->where('log.addtime' ,'>=' , strtotime($startDate));
        }
        if(!empty($endDate)){
            $info = $info->where('log.addtime' ,'<=' , strtotime($endDate));
        }
        $totalCount = $info->count();
        $info = $info->orderBy('addtime','desc')->offset($start)->limit($this->limit)->get();
        $info = !empty($info) ? $info->toArray() : array();
        
        foreach ($info as &$value){
            $value['time'] = date('Y-m-d H:i', $value['addtime']);
            $value['desc'] = subString($value['desc'], 0, 30);
        }
        return response()->json([
            'status'=>200,
            'message'=>'更新成功',
            'data'=>array(
                'pageCount'=>ceil($totalCount/$this->limit),
                'info'=>$info
            )
        ]);
    }
    /**
     * 日志详情
     */
    public function detailLog(){
        
        $id = Input::get('id');
        if(!empty($id)){
            $info = OperateLog::leftJoin('project as p', 'log.project', '=', 'p.id')
                ->leftJoin('user as u', 'log.operator','=', 'u.uid')
                ->leftJoin('apienv as e', 'log.envid', '=', 'e.id')
                ->select("log.id","p.proname","e.envname","u.username","log.desc","log.addtime")
                ->where(['log.id'=>$id])->first();
        }
        $info = !empty($info) ? $info->toArray() : array();
        return view('sys.detailLog',['info'=>$info]);
    }
    /**
     * 检查更新
     */
    public function update(){
        
        $updateUrl = Config('project.updateUrl');
        
        $updateCache = cache::get('updateCache');
        if(!empty($updateCache)){
            return response()->json($updateCache);
        }
        $HttpClient = new \Leaps\HttpClient\Adapter\Curl();
        $HttpClient->setOption(CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);
	    $updateUrl .= '?t='.base64_encode($_SERVER['HTTP_HOST']);
        $response = $HttpClient->get($updateUrl);
        
        $status = $response->getStatusCode();
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        if(!empty($data) && is_array($data) && !empty($data['version'])){
            $version = $data['version'];  //服务端版本号
            $currentVersion = Config('app.version'); //当前版本号
        }
        if(!empty($version) && !empty($currentVersion)){
            if(version_compare($version, $currentVersion, 'gt')){
                $result = array(
                    'status'    => 200,
                    'message'   => "检查到新版本{$version}",
                    'data'  => $data
                );
            }else{
                $result = array(
                    'status'    => 2010,
                    'message'   => "当前暂无更新,感谢您的关注!",
                );
            }
        }else{
            $result = array(
                'status'    => 2011,
                'message'   => "未检测到更新信息，请稍后重试!",
            );
        }
        Cache::put('updateCache', $result, 24*60);
        
        return response()->json($result);
    }
    
}
