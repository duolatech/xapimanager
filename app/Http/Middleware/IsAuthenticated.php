<?php

/**
 * 是否认证中间页，未登录时跳转至登录页
 */
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Cache;
use Closure;

class IsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        //用户登录状态判断
        $uid = $request->session()->get('uid');
        if (empty($uid)) {
            return redirect('/Login/index');
        }
        //权限检查
        $authRule = $request['sys']['AuthRule'];
        $uri = $request->path();
        $auth = $this->userAuth($uri, $authRule);
        if(!$auth){
            return response()->json(['status'=>5020, 'message'=>'您无该操作权限，请联系管理员']);
        }
        //防止快速请求
        if(!empty($request->isMethod('post'))){
            //防止post频繁快速请求
            $quick = $request->session()->get('quickTime');
            if(!empty($quick) && (time()<$quick+config('project.quickTime'))){
                return response()->json(['status'=>5010, 'message'=>'请求频繁，请稍后重试!']);
            }
        }

        return $next($request);
    }
    /**
     * 查询用户是否具有该节点权限
     * @param $node  用户节点,即path
     * @param $rule  用户拥有的权限
     */
    public function userAuth($node, $rule){
        
        $allNode = cache::get('allNode');
        $ruleid = 0;
        foreach($allNode as $value){
            $path = preg_replace(array('/\//','/{\w+?}/'), array('\/', '\w+?'), $value['path']);
            $pattern = '/^'.$path.'$/i';
            if(preg_match($pattern, $node)){
                $ruleid = $value['id'];
                break;
            }
        }
        //当前用户权限
        $result = false;
        if(!empty($rule) && is_array($rule)){
            if($rule['status']==1 && is_array($rule['rules'])){
                if(in_array($ruleid, $rule['rules'])){
                    $result = true;
                }
            }
        }
        return $result;
    }
}
