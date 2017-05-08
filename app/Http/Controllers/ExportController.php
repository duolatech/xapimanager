<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Input, Log;
use App\Models\Classify;
require_once app_path().'/tools/mpdf/vendor/autoload.php';
set_time_limit(600);
class ExportController extends Controller
{
	//Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=300;
    
    public function __construct(){
        
        //实例化mpdf
        $this->mpdf = new \Mpdf\Mpdf(['utf-8','A4','','宋体',10,0,20,10]);
    }
    /**
     * 导出分类Api
     */
    public function classify(Request $request, $cid)
    {
        //获取分类接口信息
        $content = '';
		if(!empty($cid)){
			$envid = Input::get('envid');
	        $protocol = is_HTTPS() ? 'https' : 'http';
			$url = $protocol.'://'.$_SERVER['HTTP_HOST'].'/Category/v1/classify/'.$cid.'?envid='.$envid;
			$content = $this->getPdfContent($url);
			$class = $this->getClassify($cid);
		}
		//获取站点信息
        $site = $request['sys']['Website'];
        //设置字体,解决中文乱码
        $this->mpdf->useAdobeCJK = true;
        $this->mpdf->autoLangToFont = true;
        $this->mpdf->autoScriptToLang = true;
        //设置PDF页眉内容
        $header='<table width="95%" style="margin:0 auto;border-bottom: 1px solid #4F81BD; vertical-align: middle; font-family:
                serif; font-size: 9pt; color: #000088;"><tr>
                <td width="90%" align="left" style="font-size:16px;color:#A0A0A0">'.$site['sitename'].'</td>
                <td width="10%" style="text-align: right;"></td>
                </tr></table>';
        
        //设置PDF页脚内容
        $footer='<table width="100%" style=" vertical-align: bottom; font-family:
                serif; font-size: 9pt; color: #000088;"><tr style="height:30px"></tr><tr>
                <td width="90%" align="center" style="font-size:14px;color:#A0A0A0">xApi Manager</td>
                <td width="10%" style="text-align: left;">页码：{PAGENO}/{nb}</td>
                </tr></table>';
        
        //添加页眉和页脚到pdf中
        $this->mpdf->SetHTMLHeader($header);
        $this->mpdf->SetHTMLFooter($footer);
        //设置pdf显示方式
        $this->mpdf->SetDisplayMode('fullpage');
        $classifyname = !empty($class['classifyname']) ? $class['classifyname'] : '';
        $this->mpdf->SetTitle($classifyname);
        //创建pdf文件
        //file_put_contents('a.html', $content);
        $this->mpdf->WriteHTML($content);
        //输出pdf
        $this->mpdf->Output();
        
        exit;
		
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
     * 获取pdf内容
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
