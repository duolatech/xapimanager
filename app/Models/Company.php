<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'secret';

    protected $primaryKey = 'id';

    public $timestamps = false;
    
    
}
