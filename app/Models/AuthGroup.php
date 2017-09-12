<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthGroup extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'auth_group';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function getGroupUser(){
        
        return $this->hasMany('App\Models\AuthAccess', 'group_id', 'id');
        
    }
}
