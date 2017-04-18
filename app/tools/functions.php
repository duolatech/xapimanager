<?php

/**
 * 随机生成指定长度的字符串
 * @param $len 字符串长度
 * @return string
 */
function GetRandStr($len){ 
  $chars_array = array( 
    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", 
    "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", 
    "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", 
    "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", 
    "S", "T", "U", "V", "W", "X", "Y", "Z", 
  ); 
  $charsLen = count($chars_array) - 1; 
 
  $outputstr = ""; 
  for ($i=0; $i<$len; $i++) 
  { 
    $outputstr .= $chars_array[mt_rand(0, $charsLen)]; 
  } 
  return $outputstr; 
}
/**
 * 删除数组中的键值
 * @param array $arr  待处理一维数组
 * @param array $fields 待删除键
 * @return 新数组
 */
function GetFilterArray($arr, $fields){
    
    if(!empty($fields) && is_array($fields)){
        foreach ($fields as $key){
            if(array_key_exists($key, $arr)){
                unset($arr[$key]);
            }
        }
    }
    return $arr;
}
/**
 * 文件扩展名
 * @param  $file 文件
 * @return 扩展名
 */
function getExtension($file) {
    return pathinfo ( $file, PATHINFO_EXTENSION );
}
/**
 * 加密算法
 * $string 明文或密文
 * $operation 加密ENCODE或解密DECODE
 * $key 密钥
 * $expiry 密钥有效期
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $ckey_length = 4;

    // 密匙
    // $GLOBALS['discuz_auth_key'] 这里可以根据自己的需要修改
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
/**
 * 普通加密解密算法
 * $string 明文或密文
 * $operation 加密ENCODE或解密DECODE
 * $key 密钥
 */
function normalEncrypt($string, $operation, $key){
    
    $key    =   md5($key);
    $x      =   0;
    $l      =   strlen($key);
    $char = '';
    $string = strval($string);
    switch ($operation){
        case 'ENCODE':
            $len    =   strlen($string);
            for ($i = 0; $i < $len; $i++){
                if ($x == $l){
                    $x = 0;
                }
                $char .= $key{$x};
                $x++;
            }
            $str = '';
            for ($i = 0; $i < $len; $i++){
                $str .= chr(ord($string{$i}) + (ord($char{$i})) % 256);
            }
            $result =  base64_encode($str);
            break;
        case 'DECODE':
            $string = base64_decode($string);
            $len = strlen($string);
            for ($i = 0; $i < $len; $i++){
                if ($x == $l){
                    $x = 0;
                }
                $char .= substr($key, $x, 1);
                $x++;
            }
            $str = '';
            for ($i = 0; $i < $len; $i++){
                if (ord(substr($string, $i, 1)) < ord(substr($char, $i, 1))){
                    $str .= chr((ord(substr($string, $i, 1)) + 256) - ord(substr($char, $i, 1)));
                }else{
                    $str .= chr(ord(substr($string, $i, 1)) - ord(substr($char, $i, 1)));
                }
            }
            $result = $str;
            break;
        default:
            $result = '加解密方法不存在';
            break;
    }
    
    return $result;
}
/**
 * Utf-8字符串截取函数
 *
 * @param $str 字符串
 * @param $start 开始位置
 * @param $length 长度
 * @return 截取的字符串
 */
function subString($str, $start, $length) {
    $i = 0;
    // 完整排除之前的UTF8字符
    while ( $i < $start ) {
        $ord = ord ( $str {$i} );
        if ($ord < 192) {
            $i ++;
        } elseif ($ord < 224) {
            $i += 2;
        } else {
            $i += 3;
        }
    }
    // 开始截取
    $result = '';
    while ( $i < $start + $length && $i < strlen ( $str ) ) {
        $ord = ord ( $str {$i} );
        if ($ord < 192) {
            $result .= $str {$i};
            $i ++;
        } elseif ($ord < 224) {
            $result .= $str {$i} . $str {$i + 1};
            $i += 2;
        } else {
            $result .= $str {$i} . $str {$i + 1} . $str {$i + 2};
            $i += 3;
        }
    }
    if ($i < strlen ( $str )) {
        $result .= '...';
    }
    return $result;
}
/**
 * 请求或响应字段参数排序
 * @param $data 待排序数据
 * @param $fieldname 确定个数字段
 * @return 已排序数据
 */
function fieldParamSort($data, $fieldname){

    $inside = array_keys($data[$fieldname]);
    $outside = array_keys($data);
    $result = array();
    foreach($inside as $vol){
        foreach($outside as $value){
            $result[$vol][$value] = $data[$value][$vol];
        }
    }

    return $result;
}
/**
 * 获取内存使用情况
 * @return string
 */
function memory_usage() {
    $memory     = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
    return $memory;
}
/**
 * 重新拼接url
 * @param $url 待拼接url
 * @param $param 参数
 * @return 新的url
 */
function urlSplice($url, $param=array()){
    
    $data = parse_url($url);
    $data['query'] = !empty($data['query']) ? $data['query'] : '';
    parse_str($data['query'], $arr);
    $param = array_merge($arr, $param);
    if(empty($data['path'])) $data['path'] = '';
    if(!empty($param)){
        $newUrl = $data['scheme']."://".$data['host'].$data['path']."?".http_build_query($param);
    }else{
        $newUrl = $data['scheme']."://".$data['host'].$data['path'];
    }
    
    return $newUrl;
}
/**
 * 判断是否是https
 * @return boolean 
 */
function is_HTTPS(){
    if(!isset($_SERVER['HTTPS']))  return FALSE;
    if($_SERVER['HTTPS'] === 1){  //Apache
        return TRUE;
    }elseif($_SERVER['HTTPS'] === 'on'){ //IIS
        return TRUE;
    }elseif($_SERVER['SERVER_PORT'] == 443){ //其他
        return TRUE;
    }
    return FALSE;
}