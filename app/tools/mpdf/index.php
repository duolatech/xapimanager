<?php
/*
*功能:生成用户诊断报告PDF文件
*创建时间:2013-09-12
*/

set_time_limit(0);
include './mpdf.php';
 
//实例化mpdf
$mpdf=new mPDF('utf-8','A4','','宋体',10,0,20,10);

//设置字体,解决中文乱码
$mpdf->useAdobeCJK = true;
$mpdf->autoLangToFont = true;
$mpdf->autoScriptToLang = true;
 
//获取要生成的静态文件
$html=file_get_contents('./apilist.html');
//$html=file_get_contents('http://www.openapi.com/Category/v1/classify/1?envid=1');


 
//设置PDF页眉内容
$header='<table width="95%" style="margin:0 auto;border-bottom: 1px solid #4F81BD; vertical-align: middle; font-family:
serif; font-size: 9pt; color: #000088;"><tr>
<td width="90%" align="left" style="font-size:16px;color:#A0A0A0">页眉</td>
<td width="10%" style="text-align: right;"></td>
</tr></table>';
 
//设置PDF页脚内容
$footer='<table width="100%" style=" vertical-align: bottom; font-family:
serif; font-size: 9pt; color: #000088;"><tr style="height:30px"></tr><tr>
<td width="90%" align="center" style="font-size:14px;color:#A0A0A0">xApi Manager</td>
<td width="10%" style="text-align: left;">页码：{PAGENO}/{nb}</td>
</tr></table>';

 
//添加页眉和页脚到pdf中
$mpdf->SetHTMLHeader($header);
$mpdf->SetHTMLFooter($footer);
 
//设置pdf显示方式
$mpdf->SetDisplayMode('fullpage');

$mpdf->SetTitle('接口调试');
 
//设置pdf的尺寸为270mm*397mm
//$mpdf->WriteHTML('<pagebreak sheet-size="270mm 397mm" />');
 
//创建pdf文件
$mpdf->WriteHTML($html);

 
//删除pdf第一页(由于设置pdf尺寸导致多出了一页)
//$mpdf->DeletePages(1,1);
 
//输出pdf
$mpdf->Output();
 
exit;
 
?>