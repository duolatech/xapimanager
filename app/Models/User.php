<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user';

    protected $primaryKey = 'uid';

    public $timestamps = false;
    
    /**
     * 获取用户和组信息
     * @param $field   查询字段名称
     * @param $keyword 查询字段值
     * @param 
     * @param $start   偏移量
     * @param $limit   数据条数
     */
    public function getUser($field, $keyword, $groupId, $start, $limit)
    {
        
        $where = array();
        if (! empty($field) && ! empty($keyword)) {
            $where[] = array($field, 'like', "%$keyword%");
        }
        if(!empty($groupId)){
            $where[] = array('auth_access.group_id', '=', intval($groupId));
        }
        $listObj = DB::table('user')->join('auth_access', 'user.uid', '=', 'auth_access.uid')
                ->select('user.*', 'auth_access.group_id');
        
        if(!empty($where) && is_array($where)){
            $listObj = $listObj->where($where);
        }
        
        $totalCount = $listObj->count();
        $info = $listObj->orderBy('user.ctime', 'desc')->offset($start)->limit($limit)->get();
        
        $result['totalCount'] = $totalCount;
        $result['info'] = array();
        foreach ($info as $key=>$item){
            $result['info'][] = get_object_vars($item);
        }
        
        return $result;
    }
    /**
     * 检查用户信息是否重复
     * @param $uid 用户uid
     * @param $param 用户数据
     * @return unknown
     */
    public function isRepeat($uid, $param){
        
        $this->param = $param;
        if(!empty($uid)){
            $info = $this->where('uid', '<>', $uid)->where(function($query)
            {
                $query->orWhere('username',$this->param['username'])
                ->orWhere('phone', $this->param['phone'])->orWhere('email', $this->param['email']);
            })
            ->first();
            $result = (!empty($info)) ? $info->toArray() : array();
            
        }else{
            $info = $this->where('username',$this->param['username'])
            ->orWhere('phone', $this->param['phone'])->orWhere('email', $this->param['email'])
            ->first();
            $result = (!empty($info)) ? $info->toArray() : array();
        }
        
        return $result;
    }
}
