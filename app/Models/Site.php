<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'site';
    protected $primaryKey = 'id';
    public $timestamps = false;
    

}
