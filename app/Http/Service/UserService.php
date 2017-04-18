<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class UserService{
    
    public static function getInfo($user){
        
        $result = User::where('username','=',$user)
                  ->orWhere('email','=',$user)->first()->toArray();
        if(!empty($result) && is_array($result)){
            return $result;
        }else{
            return array();
        }
    }
}
