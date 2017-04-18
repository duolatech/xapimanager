<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiList extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'apilist';

    protected $primaryKey = 'id';

    public $timestamps = false;
    
    /**
     * 获取接口列表信息
     * @param $param   搜索参数
     * @param $status  接口状态
     * @param $start   偏移量
     * @param $limit   数据条数
     */
    public function getApiDetail($param, $status, $start, $limit)
    {
        //查询条件
        if(!empty($param['envid']))  $where[] = array('apilist.envid', '=', $param['envid']);
        if(!empty($param['apiname']))  $where[] = array('apilist.apiname', 'like', "%{$param['apiname']}%");
        if(!empty($param['URI']))  $where[] = array('apidetail.URI', 'like', "%{$param['URI']}%");
        
        $listObj = DB::table('apilist')->join('apidetail', 'apilist.id', '=', 'apidetail.listid')
                ->select('apilist.classify', 'apilist.apiname', 'apilist.status as lstatus', 'apidetail.*')
                ->orderBy('apidetail.ctime', 'desc');
        //查询条件
        if(!empty($where) && is_array($where)){
            $listObj = $listObj->where($where);
        }
        //分类
        if(!empty($param['classify']) && is_array($param['classify'])){
            $listObj = $listObj->whereIn('apilist.classify', $param['classify']);
        }
        //开发人
        if(!empty($param['author']) && is_array($param['author'])){
            $listObj = $listObj->whereIn('apidetail.author', $param['author']);
        }
        //状态
        if(!empty($status) && is_array($status)){
            $listObj = $listObj->whereIn('apidetail.status', $status);
        }
        
        $totalCount = $listObj->count();
        $info = $listObj->offset($start)->limit($limit)->get();
        
        $result['info'] = array();
        $result['totalCount'] = $totalCount;
        foreach ($info as $key=>$item){
            $result['info'][] = get_object_vars($item);
        }
    
        return $result;
    }
    
}
