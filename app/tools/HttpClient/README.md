##HttpClient 通过HTTP/HTTPS协议获取数据
###安装
```json
"require" : {
    "php" : ">=5.4.0",
    "leaps/httpclient": "1.4.9"
}
```
如果您修改了leaps/httpclient的代码，我非常的欢迎您把您的修改提交给我，如果您的使用中遇到任何问题欢迎在右侧的issues提交给我，我将尽快修复，本项目的更新非常频繁，1.2和1.3除了响应类不兼容以外基本都兼容，如果您使用的是1.2版本想升级到1.3版本，只需要继续使用1.2的响应类即可。
###基本使用

这个组件极易使用：

```php
<?php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

也可以使用批量获取不同的网页内容：

```php
<?php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$responses = $HttpClient->get(['http://www.baidu.com/','http://www.qq.com']);
foreach($responses as $response){
    echo $response->getContent();
}
```

###设置User Agent
默认情况下，如果是WEB形式使用的本组件，那么UserAgent是取的用户浏览器的，在cli模式下这个值是PHP版本号。

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->setUserAgent('test')；
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```


###设置cookie内容，$cookie为字符串，多个cookie请用;隔开


```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
//$HttpClient->setCookie(['a'=>'3']);
$HttpClient->setCookie('a=1;b=a;c[0]=1;c[1]=2');
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

###设置代理服务器地址

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->setHttpProxy('host','port');
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

###设置基本认证的用户名和密码

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->setAuthorization('username','password');
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

###设置引用页

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->setReferer('http://www.test.com/');
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

###设置请求的服务器的IP，这样可避免请求域名时DNS解析

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
// 这样设置请求页面并不会通过DNS解析获取百度服务器的数据，而是直接请求127.0.0.1（即本机）的服务器的数据
$HttpClient->setHostIp('127.0.0.1');
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

###设置并发请求时最大列队数量，系统默认为100。

HttpClient是支持并发请求的，详细可查看下面的get()方法。如果同时请求一个服务器，在一瞬间会对被请求服务器造成巨大压力，也会对本服务器增加网络IO压力，
所以这个参数可以控制同时并发的数量上限，当达到上限后，列队将等待执行完毕一个追加一个插入列队。

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->setMultiMaxNum(100);
$response = $HttpClient->get(['http://www.baidu.com/']);
echo $response->getContent();
```

###设置其它参数

用以弥补HttpClient类中不存在的方法，具体请看具体驱动的方法，比如采用CURL的话，其实就相当于CURL的setOption()方法

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->setOption(CURLOPT_TIMEOUT,30);
$response = $HttpClient->get('http://www.baidu.com/');
echo $response->getContent();
```

###get请求

用GET方法请求一个(或多个)页面，这样可以大大缩短API请求时间，并可以设置超时时间，单位：秒
支持并发进程请求，并发请求的特点：比如需要同时请求100个页面，传统的是一个一个载入，假设每个页面需要0.1秒，那么100个页面就需要耗时10秒，而通过并发的方式，100个页面理论上也就是0.1秒就可以同时载入完成了，效率非常高。

单个URL直接返回请求的内容的对象，多个URL则返回以URL为key的数组

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
// 请求单个页面
echo $HttpClient->get('http://www.baidu.com/',3)->getContent();

// 请求多个页面
$urls = array
(
    'http://www.baidu.com/',
    'http://www.google.com/',
    'http://www.sina.com.cn/test.html',
);
// 返回已url为key的数组，注意，前后顺序有可能跟$urls中的不一样
print_r($HttpClient->get($urls));
```

###post请求

用POST方法提交数据，支持多个页面同时请求
// 请求单个页面

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->post('http://www.baidu.com/',array('a'=>1,'b'=>1));
    
// 请求多个页面
$urls = array
(
    'http://www.baidu.com/',
    'http://www.google.com/',
);
$vars = array
(
    array('a'=>1,'b'=>1),   //对应 http://www.baidu.com/
    array('c'=>1,'d'=>1),   //对应 http://www.google.com/
);
print_r($HttpClient->post($urls,$vars));
```
###put请求

用PUt方法提交数据，支持多个页面同时请求
// 请求单个页面

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->put('http://www.baidu.com/',array('a'=>1,'b'=>1));
    
// 请求多个页面
$urls = array
(
    'http://www.baidu.com/',
    'http://www.google.com/',
);
$vars = array
(
    array('a'=>1,'b'=>1),   //对应 http://www.baidu.com/
    array('c'=>1,'d'=>1),   //对应 http://www.google.com/
);
print_r($HttpClient->put($urls,$vars));
```

###delete请求

用DELETE方法请求一个(或多个)页面，这样可以大大缩短API请求时间，并可以设置超时时间，单位：秒
支持并发进程请求，并发请求的特点：比如需要同时请求100个页面，传统的是一个一个载入，假设每个页面需要0.1秒，那么100个页面就需要耗时10秒，而通过并发的方式，100个页面理论上也就是0.1秒就可以同时载入完成了，效率非常高。

单个URL直接返回请求的内容的对象，多个URL则返回以URL为key的数组

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
// 请求单个页面
echo $HttpClient->delete('http://www.baidu.com/',3)->getContent();

// 请求多个页面
$urls = array
(
    'http://www.baidu.com/',
    'http://www.google.com/',
    'http://www.sina.com.cn/test.html',
);
// 返回已url为key的数组，注意，前后顺序有可能跟$urls中的不一样
print_r($HttpClient->delete($urls));
```
###Upload上传

用POST方法提交上传数据，不支持多个页面同时请求.

```php
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->upload('http://localhost/upload', 'pic','/tmp/test.jpg',['a'=>1,'b'=>1]);
//或者    
//Create an instance
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$HttpClient->addFile('pic','/tmp/test.jpg','image/jpg');
$HttpClient->post('http://localhost/upload', ['a'=>1,'b'=>1]);
```

###高级响应（Response）

```php
$HttpClient = new \Leaps\HttpClient\Adapter\Curl();
$response = $HttpClient->get('http://www.baidu.com/');

//获取响应的文档类型
echo $response->getContentType();

//获取响应的文档编码(当响应头和返回的HTML文档中没有编码信息时该方法获取不到正确的编码)
echo $response->getCharSet();

//获取响应的文档后缀名，(根据响应的文档类型来匹配后缀名，方便在下载文档后另存)
echo $response->getContentFormat();

//获取响应的状态码（如200）
echo $response->getStatusCode();

//获取原始的响应头
echo $response->getRawHeader();

//获取解析过的响应头Key->value形式
echo $response->getHeaders();

//获取指定响应头
echo $response->getHeader($name);

//获取解析过的Cookie集合，数组形式
echo $response->getCookies();

//获取指定的Cookie值
echo $response->getCookie($name);

//获取本次请求消耗的时间
echo $response->getTime()

//获取响应的内容
echo $response->getContent();

//获取HTML文档Head中的title和meta标签数组
echo $response->getHeadTags();

//是否是有效的HTTP响应码
echo $response->isInvalid();

//是否是成功的响应（响应码为200-300之间视为成功）
echo $response->isSuccessful();

//是否是重定向响应(300-400)
echo $response->isRedirection();

//是否是客户端错误的响应(400-500)
echo $response->isClientError();

//是否是服务端错误的响应（500-600）
echo $response->isServerError();

//是否是200
echo $response->isOk();

//是否是403
echo $response->isForbidden();

//是否是404
echo $response->isNotFound();

//是否是201,04,304
echo $response->isEmpty();
```
