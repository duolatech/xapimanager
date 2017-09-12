<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiEnv extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'apienv';

    protected $primaryKey = 'id';

    public $timestamps = false;
    
}
