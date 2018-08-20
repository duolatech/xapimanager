<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Input, Log;
use App\Models\Classify;
require_once app_path().'/tools/HttpClient/vendor/autoload.php';
set_time_limit(600);
class ExportController extends Controller
{
	//Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=300;
    
    /**
     * 导出分类Api
     */
    public function subClassify(Request $request, $cid)
    {
        //获取分类接口信息
        $content = '';
		if(!empty($cid)){
			$envid = Input::get('envid');
	        $protocol = is_HTTPS() ? 'https' : 'http';
			$url = $protocol.'://'.$_SERVER['HTTP_HOST'].'/Category/v1/subClassify/'.$cid.'?envid='.$envid;
			$content = $this->getPdfContent($url);
			$class = $this->getClassify($cid);
		}
		$classifyname = !empty($class['classifyname']) ? $class['classifyname'] : '无';
		$filename = iconv('utf-8', 'gb2312', $classifyname);  
	    $content =  iconv('utf-8', 'gb2312', $content);
        header('pragma:public');  
        header('Content-type:application/vnd.ms-word;charset=utf-8;name="'.$filename.'".doc');  
        header("Content-Disposition:attachment;filename=$filename.doc");//attachment新窗口打印inline本窗口打印  
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office"  
        xmlns:w="urn:schemas-microsoft-com:office:word"  
        xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="Content-Type" content="text/html; charset=gb2312"/>';//这句不能少，否则不能识别图片  
        echo $html;
        echo $content;exit;
    }
    /**
     * 查询分类信息
     * @param $cid 分类id
     */
    public function getClassify($cid){
    
        $data = Classify::where('id',$cid)->where('status',1)->first();
        $info = !empty($data) ? $data->toArray() : array();
    
        return $info;
    }
    /**
     * 获取word内容
     * @param $url 内容页url
     */
    public function getPdfContent($url){
        
        $HttpClient = new \Leaps\HttpClient\Adapter\Curl();
        $HttpClient->setOption(CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT);
        $httpCookie = $_SERVER['HTTP_COOKIE'];
        $HttpClient->setCookie($httpCookie);
        $response = $HttpClient->get($url);
        $status = $response->getStatusCode();
        $content = ($status==200) ? $response->getContent() : '';
        
        return $content;
    }
    

}
