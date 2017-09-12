<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'message';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public static function sendMessage($info){
        
        if(!empty($info) && is_array($info)){
            $data = array(
                'sender'    => $info['sender'],
                'receiver'  => $info['receiver'],
                'pid'   => $info['pid'],
                'subject'   => $info['subject'],
                'content'   => $info['content'],
                'sendtime'  => $info['sendtime'],
                'isread'    => 2
            );
            $id = self::insertGetId($data);
        }
        
        return !empty($id) ? $id : 0;
    } 

}
