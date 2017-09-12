<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Input, Log;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * 注册页面
     */
    public function index(){
        
        $data = array(
            'from'  => mt_rand(10,40),
            'to'    => mt_rand(50, 100)
        );
        Session::put('captcha', $data);
        
        return view('register',['data'=>$data]);
        
    }
    /**
     * 注册用户
     */
    public function store(userRequest $request){
        
        //验证码检查
        if (!$this->captcha()){
            return response()->json(['status'=>4010, 'message'=>'验证码错误']);
        }
        //用户检查
        if($this->isExist()){
            return response()->json(['status'=>4011, 'message'=>'用户已存在']);
        }
        //保存用户信心
        $id = $this->saveUser($_POST);
        if($id){
            return response()->json(['status'=>200, 'message'=>'注册成功，请联系管理员审核']);
        }else{
            return response()->json(['status'=>4013, 'message'=>'注册失败，请稍后重试']);
        }
           
    }
    /**
     * 注册时用户信息检查
     */
    public function check(){
        
        $data = array();
        $field = array('username', 'phone', 'email');
        foreach ($field as $value){
            if(!empty($_POST[$value])){
                $data[$value] = $_POST[$value];
            }
        }
        if(!empty($data)){
            $info = User::where($data)->first();
        }
        $bool = !empty($info) ? false : true;
        
        return response()->json($bool);
    }
    /**
     * 检查用户是否存在
     * @return boolean
     */
    public function isExist(){
        
        $username = Input::get('username');
        $phone = Input::get('phone');
        $email = Input::get('email');
        $info = User::where('username', $username)->orWhere('phone', $phone)->orWhere('email',$email)->first();
        
        return !empty($info) ? true : false;
    }
    /**
     * 保存用户信息
     * @param $post 用户数据
     * @return 用户id
     */
    public function saveUser($post){
    
        $user = new User();
        $user->ctime = time();
        $user->username = $post['username'];
        $user->salt = GetRandStr(6);
        $user->password = md5(md5($post['password']).$user->salt);
        $user->phone = $post['phone'];
        $user->email = $post['email'];
        $user->status = 3; //1在职，2离职，3待激活
        $user->save();
    
        return $user->uid;
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
