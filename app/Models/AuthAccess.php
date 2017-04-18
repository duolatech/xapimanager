<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthAccess extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'auth_access';
    protected $primaryKey = 'uid';
    public $timestamps = false;
    
    /**
     * 获取关联到用户组的规则
     */
    public function getGroupRule(){
    
        return $this->hasOne('App\Models\AuthGroup', 'id', 'group_id');
    }
}
