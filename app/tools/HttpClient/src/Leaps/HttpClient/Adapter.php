<?php
// +----------------------------------------------------------------------
// | Leaps Framework [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011-2014 Leaps Team (http://www.tintsoft.com)
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author XuTongle <xutongle@gmail.com>
// +----------------------------------------------------------------------
namespace Leaps\HttpClient;

abstract class Adapter
{
	/**
	 * 响应数据寄存器
	 *
	 * @var array
	 */
	protected $httpData = [ ];

	/**
	 * User Agent 浏览器的身份标识
	 *
	 * @var string
	 */
	protected $userAgent;

	/**
	 * 页面来源
	 *
	 * @var string
	 */
	protected $referer;

	/**
	 * 携带的Cookie
	 *
	 * @var string
	 */
	protected $cookie;
	protected $cookieJar = false;
	protected $files = [ ];
	protected $hostIp;
	protected $header = [ ];
	protected $option = [ ];
	protected $timeout = 30;

	/**
	 * 待Post提交的数据
	 *
	 * @var array
	 */
	protected $postData = [ ];

	/**
	 * 多列队任务进程数，0表示不限制
	 *
	 * @var int
	 */
	protected $multiExecNum = 20;

	/**
	 * 默认请求方法
	 *
	 * @var string
	 */
	protected $method = 'GET';

	/**
	 * 默认连接超时时间，毫秒
	 *
	 * @var int
	 */
	protected $connectTimeout = 3000;
	protected $proxyHost;
	protected $proxyPort;
	protected $authorizationToken;

	/**
	 * 设置，获取REST的类型
	 *
	 * @param string $method GET|POST|DELETE|PUT 等，不传则返回当前method
	 * @return string
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setMethod($method = null)
	{
		if (null === $method)
			return $this->method;
		$this->method = strtoupper ( $method );
	}

	/**
	 * 设置Header
	 *
	 * @param array $header
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setHeader($item, $value)
	{
		$this->header = array_merge ( $this->header, [
				$item . ": " . $value
		] );
	}

	/**
	 * 设置Header
	 *
	 * @param array $header
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setHeaders($headers)
	{
		$this->header = array_merge ( $this->header, ( array ) $headers );
	}

	/**
	 * 设置代理服务器访问
	 *
	 * @param string $host
	 * @param string $port
	 * @return \Leaps\HttpClient\HttpClient
	 */
	public function setHttpProxy($host, $port)
	{
		$this->proxyHost = $host;
		$this->proxyPort = $port;
	}

	/**
	 * 设置IP
	 *
	 * @param string $ip
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setHostIp($ip)
	{
		$this->hostIp = $ip;
	}

	/**
	 * 设置User Agent
	 *
	 * @param string $userAgent
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = $userAgent;
	}

	/**
	 * 设置Http Referer
	 *
	 * @param string $referer
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setReferer($referer)
	{
		$this->referer = $referer;
	}

	/**
	 * 设置Cookie
	 *
	 * @param string $cookie
	 * @return \Leaps\HttpClient\Adapter
	 */
	public function setCookie($cookie)
	{
		$this->cookie = $cookie;
	}
	
	/**
	 * 设置CookieJar
	 *
	 * @param string $cookieJar
	 * @return \Leaps\HttpClient\Adapter
	 */
	public function setCookieJar($cookieJar)
	{
		$this->cookieJar = $cookieJar;
	}

	/**
	 * 设置多个列队默认排队数上限
	 *
	 * @param int $num
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setMultiMaxNum($num = 0)
	{
		$this->multiExecNum = ( int ) $num;
	}

	/**
	 * 设置超时时间
	 *
	 * @param int $timeoutp
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * 重置设置
	 */
	public function reset()
	{
		$this->option = [ ];
		$this->header = [ ];
		$this->hostIp = null;
		$this->files = [ ];
		$this->cookie = null;
		$this->referer = null;
		$this->method = 'GET';
		$this->postData = [ ];
	}

	/**
	 * 获取结果数据
	 */
	public function getResutData()
	{
		return $this->httpData;
	}

	/**
	 * HTTP GET方式请求
	 *
	 * @param string $url
	 * @return \Leaps\HttpClient\Response
	 */
	public function get($url)
	{
		$this->getRequest ( $url );
		$data = $this->getResutData ();
		if (is_array ( $url )) {
			// 如果是多个URL
			$result = [ ];
			foreach ( $data as $key => $item ) {
				$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
				$result [$key] = $reflection->newInstanceArgs ( [
						$item
				] );
			}
		} else {
			$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
			$result = $reflection->newInstanceArgs ( [
					$data [$url]
			] );
		}
		return $result;
	}

	/**
	 * Http POST方式请求
	 *
	 * @param string $url
	 * @param string $data
	 * @return \Leaps\HttpClient\Response
	 */
	public function post($url, $datas = [])
	{
		$this->postRequest ( $url, $datas );
		$data = $this->getResutData ();
		if (is_array ( $url )) {
			// 如果是多个URL
			$result = [ ];
			foreach ( $data as $key => $item ) {
				$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
				$result [$key] = $reflection->newInstanceArgs ( [
						$item
				] );
			}
		} else {
			$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
			$result = $reflection->newInstanceArgs ( [
					$data [$url]
			] );
		}
		return $result;
	}

	/**
	 * PUT方式请求
	 *
	 * @param string $url
	 * @param string、array $data
	 * @return \Leaps\HttpClient\Response
	 */
	public function put($url, $datas)
	{
		$this->putRequest ( $url, $datas );
		$data = $this->getResutData ();
		if (is_array ( $url )) {
			// 如果是多个URL
			$result = [ ];
			foreach ( $data as $key => $item ) {
				$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
				$result [$key] = $reflection->newInstanceArgs ( [
						$item
				] );
			}
		} else {
			$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
			$result = $reflection->newInstanceArgs ( [
					$data [$url]
			] );
		}
		return $result;
	}

	/**
	 * DELETE方式请求
	 *
	 * @param $url
	 * @param $data
	 * @param $timeout
	 * @return \Leaps\HttpClient\Response
	 */
	public function delete($url)
	{
		$this->deleteRequest ( $url );
		$data = $this->getResutData ();
		if (is_array ( $url )) {
			// 如果是多个URL
			$result = [ ];
			foreach ( $data as $key => $item ) {
				$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
				$result [$key] = $reflection->newInstanceArgs ( [
						$item
				] );
			}
		} else {
			$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Response" );
			$result = $reflection->newInstanceArgs ( [
					$data [$url]
			] );
		}
		return $result;
	}

	/**
	 * 上传文件
	 *
	 * 注意，使用 `addFile()` 上传文件时，必须使用post方式提交
	 *
	 * upload('http://localhost/upload', 'pic','/tmp/test.jpg');
	 *
	 * @param $url
	 * @param $name string 上传的文件的key，默认为 `file`
	 * @param $fileName string
	 * @param null $post
	 * @return Result
	 */
	public function upload($url, $name, $fileName, $post = [])
	{
		return $this->addFile ( $name, $fileName )->post ( $url, $post );
	}

	/**
	 * 添加上传文件
	 *
	 * HttpClient::factory()->addFile('img','/tmp/test.jpg');
	 *
	 * @param $file_name string 文件路径
	 * @param $name string 名称
	 * @return $this
	 */
	public function addFile($name, $fileName, $mimeType = '')
	{
		$this->_addFile ( $name, $fileName, $mimeType );
		return $this;
	}
	abstract public function setAuthorization($username, $password);
	abstract public function getRequest($url);
	abstract public function postRequest($url, $vars);
	abstract public function putRequest($url, $vars);
	abstract public function deleteRequest($url);
	abstract public function _addFile($fileName, $name);
}
