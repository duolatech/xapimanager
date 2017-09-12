<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Help extends Model{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'help';
    protected $primaryKey = 'id';
    public $timestamps = false;
    

}
