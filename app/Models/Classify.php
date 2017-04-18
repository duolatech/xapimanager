<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classify extends Model{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'classify';
    protected $primaryKey = 'id';
    public $timestamps = false;
    

}
