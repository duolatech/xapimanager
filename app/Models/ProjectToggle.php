<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectToggle extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'project_toggle';

    protected $primaryKey = 'id';

    public $timestamps = false;
    
    
}
