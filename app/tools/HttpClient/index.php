<?php
//包含自动加载文件
require './vendor/autoload.php';

$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
//$response = $HttpClient->get('http://mapi.baojinjinfu.com/');
//echo $response->getContent();

function login_post($url, $cookie, $post){
$ch = curl_init(); //初始化curl模块
curl_setopt($ch, CURLOPT_URL, $url); //登录提交的地址
curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0); //是否自动显示返回的信息
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); //设置cookie信息保存在指定的文件夹中
curl_setopt($ch, CURLOPT_POST, 1); //以POST方式提交
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要执行的信息
curl_exec($ch);	//执行CURL
curl_close($ch);
}

function get_content($url, $cookie){
$ch = curl_init(); //初始化curl模块
curl_setopt($ch, CURLOPT_URL, $url); //登录提交的地址
curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否自动显示返回的信息
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);//设置cookie信息保存在指定的文件夹中
$rs = curl_exec($ch);	//执行curl转去页面内容
curl_close($ch);
return $rs; //返回字符串
}

$post = array(
'username' => 'xiaogang',
'pass' => '12345',	
'_submit' => '登录'
);
$url = "http://www.test.com/test2.php";	//登录地址， 和原网站一致
$cookie = dirname(__FILE__).'/cookie_ydma.txt'; //设置cookie保存的路径
$url2 = "http://www.test.com/test.php";	//登录后要获取信息的地址

login_post($url, $cookie, $post);	//调用模拟登录
$content = get_content($url2, $cookie); //登录后，调用get_content()函数获取登录后指定的页面信息

//@unlik($cookie);	//删除cookie文件
file_put_contents('save.txt', $content);	//保存抓取的页面内容

exit;


















$response = $HttpClient->post('http://www.test.com/test.php',array('username'=>'xiaoming','pass'=>123456));
$m = $response->getContent();
var_dump($m);
var_dump("-----------------");
header('Location:http://www.test.com/test.php');





exit;