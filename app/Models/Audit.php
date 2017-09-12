<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Audit extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'audit';

    protected $primaryKey = 'did';

    public $timestamps = false;
    
    
}
