<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthData extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'auth_data';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    
}
