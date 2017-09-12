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
    
    /**
     * 递归查询所有子分类
     * @param $proid 当前项目id
     * @param $pid 父级id
     * @return 可用菜单
     */
    public static function getClassify($proid, $pid){
    
        $data = Classify::where(['proid'=>$proid,'pid'=>$pid,'status'=>1])->orderBy('addtime')->get();
        $info = !empty($data) ? $data->toArray() : array();
        foreach ($info as &$value){
            $value['child']=self::getClassify($proid, $value['id']);
        }
        return $info;
    }
    

}
