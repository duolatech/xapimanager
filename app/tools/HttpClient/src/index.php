<?php
namespace app;
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();