<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Input, Validator, Session, Log;
use App\Models\AuthGroup;
use App\Models\AuthAccess;
use App\Models\User;

class PersonalController extends Controller
{
    
    /**
     * 个人资料
     */
    public function profile(Request $request)
    {
        $uid = intval(session::get('uid'));
        $info = array();
        if(!empty($uid)){
            $user = User::where('uid',$uid)->first();
            $info['user'] = !empty($user) ? $user->toArray() : array();
            $userGroup  = AuthAccess::find($uid);
            $info['userGroup'] = !empty($userGroup) ? $userGroup->toArray() : array();
            $group_id = $info['userGroup']['group_id'];
        }
        //查询可用的用户组
        $authGroup = AuthGroup::where('id', $group_id)->get();
        $authGroup = !empty($authGroup) ? $authGroup->toArray() : array();
        $group = array();
        foreach ($authGroup as $value){
            if($value['status']==1){
                $group[$value['id']] = $value['groupname'];
            }
        }
        $info['group'] = $group;
        
        return view('Personal.profile',['info'=>$info]);
    }

}
