<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debug extends Model{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'debug';
    protected $primaryKey = 'id';
    public $timestamps = false;


}