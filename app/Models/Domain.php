<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'domain';
    protected $primaryKey = 'id';
    public $timestamps = false;


}