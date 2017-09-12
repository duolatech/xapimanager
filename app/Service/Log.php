<?php

namespace App\Service;

class Log{
	
    /**
     * 日志记录
     * @param array $data 日志信息
     */
	public function writeLog($data){
    
        $log = new Log;
        $log->project = $data['project'];
        $log->envid = $data['envid'];
        $log->operator = $data['operator'];
        $log->desc = $data['desc'];
        $log->addtime = $data['addtime'];
        $log->save();
        
        return $log->id;
    }
}
