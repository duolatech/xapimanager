<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Models\ProjectToggle;
use App\Models\Project;
use App\Models\ApiEnv;
use Input, Log;
use App\Models\User;

class LoginController extends Controller
{
    public function index(Request $request){
        
        if(!empty($request->isMethod('post'))){
            
            if (!$this->captcha()){
                return response()->json(['status'=>4010, 'message'=>'验证码错误']);
            }else{
                //查询用户信息
                $user = Input::get('user');
                $userInfo = User::where(function($query) use($user){
                    $query->where('username','=', $user)->orWhere('email','=',$user);
                })->first();
                $userInfo = !empty($userInfo) ? $userInfo->toArray() : array();
                if(!empty($userInfo) && $userInfo['status']==1){
                    if($userInfo['password'] == md5(Input::get('pass').$userInfo['salt'])){
                        Session::put('uid', $userInfo['uid']);
                        Session::put('username', $userInfo['username']);
                        if(empty($userInfo['avatar'])){
                            $userInfo['avatar'] = '/img/avatar.jpg';
                        }
                        Session::put('avatar', $userInfo['avatar']);
                        $this->project($userInfo['uid']);
                        return response()->json(['status'=>200, 'message'=>'登录成功']);
                    }else{
                        return response()->json(['status'=>4011, 'message'=>'用户名或密码错误']);
                    }
                }elseif(!empty($userInfo) && $userInfo['status']==3){
                    return response()->json(['status'=>4013, 'message'=>'等待管理员审核，请稍等！']);
                }else{
                    return response()->json(['status'=>4012, 'message'=>'用户名不存在，请联系管理员']);
                }
                
            }
        }else{
            $data = array(
                'from'  => mt_rand(10,40),
                'to'    => mt_rand(50, 100),
            );
            Session::put('captcha', $data);
        }
        
        return view('login',['data'=>$data]);
        
    }
    /**
     * 用户未指定项目时为用户指定一个当前项目
     */
    public function project($uid){

        $project = Project::where(['attribute'=>1,'status'=>1]);
            if(!empty($proids) && is_array($proids)){
                $project = $project->orWhere(function ($query) use ($proids){
                    $query->whereIn('id', $proids)->where(['status'=>1]);
                });
            }
        $project = $project->get();
        $project = !empty($project) ? $project->toArray() : array();

        $arr = array_shift($project);
        $proid = $arr['id'];
        $data = ProjectToggle::where(array('uid'=>$uid, 'proid'=>$proid))->first();
        if(empty($data)){
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
    /**
     * 退出系统
     * @param Request $request
     */
    public function logout(Request $request){
        
        //清除所有缓存
        Cache::flush();
        $request->session()->flush();
        
        return redirect('Login/index');
    }
    /**
     * 检查验证码是否正确
     */
    public function captcha(){
        
        $rand = Input::get('rand');
        $rangeslider = Input::get('rangeslider');
        $captcha = Session::get('captcha');
        if(!empty($captcha) && $rand>=0 && $rand<=100){
            if($rangeslider==md5($captcha['from'].'#'.$captcha['to'])){
                return true;
            }
        }
        return false;
    }
}
