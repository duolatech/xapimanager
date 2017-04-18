<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Input, Validator, Captcha, Log;
use App\Models\User;

class LoginController extends Controller
{
    
    public function __construct()
    {
        //$this->middleware('guest', ['except' => 'logout']);
    }
    public function index(Request $request){
        
        if(!empty($request->isMethod('post'))){
            //验证码的名称应为如下键（captcha）
            $rules = ['captcha' => 'required|captcha'];
            $validator = Validator(Input::all(), $rules);
            if ($validator->fails()){
                return response()->json(['status'=>4010, 'message'=>'验证码错误']);
            }else{
                //查询用户信息
                $user = Input::get('user');
                $userInfo = User::where('status',1)->where(function($query) use($user){
                    $query->where('username','=', $user)->orWhere('email','=',$user);
                })->first();
                $userInfo = !empty($userInfo) ? $userInfo->toArray() : array();
                if(!empty($userInfo)){
                    if($userInfo['password'] == md5(Input::get('pass').$userInfo['salt'])){
                        Session::put('uid', $userInfo['uid']);
                        Session::put('username', $userInfo['username']);
                        Session::put('avatar', $userInfo['avatar']);
                        return response()->json(['status'=>200, 'message'=>'登录成功']);
                    }else{
                        return response()->json(['status'=>4011, 'message'=>'用户名或密码错误']);
                    }
                }else{
                    return response()->json(['status'=>4012, 'message'=>'用户名不存在，请联系管理员']);
                }
                
            }
        }
        return view('login');
        
    }
    /**
     * 退出系统
     * @param Request $request
     */
    public function logout(Request $request){
        
        $request->session()->flush();
        
        return redirect('Login/index');
    }
}
