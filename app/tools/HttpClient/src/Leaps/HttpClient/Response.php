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

class Response
{
	/**
	 * 原始的相应头
	 *
	 * @var string
	 */
	private $rawHeaders;

	/**
	 * 解析后的Header集合
	 *
	 * @var array
	 */
	private $headers = [ ];

	/**
	 * 响应状态码
	 *
	 * @var int
	 */
	private $statusCode = 0;

	/**
	 * 响应内容
	 *
	 * @var string
	 */
	private $content;

	/**
	 * 响应的内容类型
	 *
	 * @var string
	 */
	private $contentType;

	/**
	 * 响应的内容格式
	 *
	 * @var string
	 */
	private $contentFormat;

	/**
	 * 响应的内容编码
	 *
	 * @var string
	 */
	private $charset;

	/**
	 * Cookie集合
	 *
	 * @var array
	 */
	private $cookies = [ ];

	/**
	 * 使用时间单位秒
	 *
	 * @var float
	 */
	private $time = 0;

	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $response)
	{
		if (isset ( $response ['code'] )) {
			$this->statusCode = $response ['code'];
		}
		if (isset ( $response ['time'] )) {
			$this->time = $response ['time'];
		}
		if (isset ( $response ['data'] )) {
			$this->content = $response ['data'];
		}
		if (isset ( $response ['rawHeader'] )) {
			$this->rawHeaders = $response ['rawHeader'];
		}
		if (isset ( $response ['header'] ) && is_array ( $response ['header'] )) {
			foreach ( $response ['header'] as $item ) {
				if (empty ( $item ))
					continue;
				if (strpos ( $item, ':' ) !== false) {
					list ( $key, $value ) = explode ( ': ', $item, 2 );
					if ($key == 'Set-Cookie') { // Cookie 特殊处理
						$this->headers [$key] [] = $value;
						$this->resolveCookie ( $value );
					} else {
						if ($key == 'Content-Type' || $key == 'Content-type') {
							if (($pos = strpos ( $value, ';' )) !== false) {
								$this->contentType = substr ( $value, 0, $pos ); // 大部分情况下出现在GBK的网页中。。
							} else {
								$this->contentType = $value;
							}
						}
						$this->headers [$key] = $value;
					}
				} else {
					$this->headers [] = $item;
				}
			}
		}
	}

	/**
	 * 获取响应的文档类型
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * 获取结果编码
	 *
	 * @return string
	 */
	public function getCharSet()
	{
		if (! $this->charset) {
			// 应该判断内容是否是文本类型
			if (strpos ( $this->contentType, 'text/' ) !== false) {
				if (($this->getContentFormat () == 'htm' || $this->getContentFormat () == 'html') && preg_match ( "/<meta.+?charset=[^\\w]?([-\\w]+)/i", $this->content, $match )) {
					$this->charset = strtoupper ( $match [1] );
				} else { // 检测中文常用编码
					$this->charset = strtoupper ( mb_detect_encoding ( $this->content, [ 'ASCII','CP936','GB18030','UTF-8','BIG-5' ] ) );
				}
			}
		}
		return $this->charset;
	}

	/**
	 * 获取内容后缀
	 */
	public function getContentFormat()
	{
		if (! $this->contentFormat) {
			$this->contentFormat = MimeType::getSuffix ( $this->contentType );
		}
		return $this->contentFormat;
	}

	/**
	 * 是否是有效的响应码
	 *
	 * @return boolean
	 */
	public function isInvalid()
	{
		return $this->getStatusCode () < 100 || $this->getStatusCode () >= 600;
	}

	/**
	 * 是否是成功的响应
	 *
	 * @return boolean
	 */
	public function isSuccessful()
	{
		return $this->getStatusCode () >= 200 && $this->getStatusCode () < 300;
	}

	/**
	 * 是否是重定向响应
	 *
	 * @return boolean
	 */
	public function isRedirection()
	{
		return $this->getStatusCode () >= 300 && $this->getStatusCode () < 400;
	}

	/**
	 * 是否请求客户端错误
	 *
	 * @return boolean
	 */
	public function isClientError()
	{
		return $this->getStatusCode () >= 400 && $this->getStatusCode () < 500;
	}

	/**
	 * 服务端是否发生错误
	 *
	 * @return boolean
	 */
	public function isServerError()
	{
		return $this->getStatusCode () >= 500 && $this->getStatusCode () < 600;
	}

	/**
	 * 是否响应成功
	 *
	 * @return boolean
	 */
	public function isOk()
	{
		return $this->getStatusCode () == 200;
	}

	/**
	 * 是否是403
	 *
	 * @return boolean
	 */
	public function isForbidden()
	{
		return $this->getStatusCode () == 403;
	}

	/**
	 * 是否是404
	 *
	 * @return boolean
	 */
	public function isNotFound()
	{
		return $this->getStatusCode () == 404;
	}

	/**
	 * 是否是空响应
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return in_array ( $this->getStatusCode (), [ 201,204,304 ] );
	}

	/**
	 * 获取响应状态码
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * 获取原始的响应头
	 *
	 * @return string
	 */
	public function getRawHeader()
	{
		return $this->rawHeaders;
	}

	/**
	 * 获取Header集合
	 *
	 * @param string $key
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * 获取Header
	 *
	 * @param string $key
	 * @return array
	 */
	public function getHeader($name)
	{
		if (isset ( $this->headers [$name] )) {
			return $this->headers [$name];
		}
		return false;
	}

	/**
	 * 获取服务器类型
	 */
	public function getServer()
	{
		if (isset ( $this->headers ['Server'] )) {
			return $this->headers ['Server'];
		} else {
			return 'Unknown';
		}
	}

	/**
	 * 获取Cookie集合
	 *
	 * @param string $key
	 * @return multitype:
	 */
	public function getCookies()
	{
		return $this->cookies;
	}

	/**
	 * 获取Cookie内容
	 *
	 * @param string $key
	 * @return multitype:
	 */
	public function getCookie($key)
	{
		if (isset ( $this->cookies [$key] )) {
			return $this->cookies [$key];
		}
		return false;
	}

	/**
	 * 获取请求消耗时间
	 *
	 * @return number
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * 获取响应内容
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * 魔术方法，输出数组
	 *
	 * @return string
	 */
	public function getContentAsArray()
	{
		if ($this->getContentFormat () == 'json') {
			return json_decode ( $this->content, true );
		}
		return [ ];
	}

	/**
	 * 魔术方法，输出数组
	 *
	 * @return string
	 */
	public function getContentAsObject()
	{
		if ($this->getContentFormat () == 'json') {
			return json_decode ( $this->content );
		}
		return new \stdClass ();
	}

	/**
	 * 提取所有的 Head 标签返回一个数组
	 */
	public function getHeadTags()
	{
		$result = [ ];
		if (is_string ( $this->content ) && ! empty ( $this->content )) {
			if (preg_match ( "/<head>(.*)<\/head>/si", $this->content, $head )) {
				if ($this->getCharSet () != 'UTF-8') { // 转码
					$head [1] = mb_convert_encoding ( $head [1], 'UTF-8', $this->getCharSet () );
				}
				// 解析title
				if (preg_match ( '/<title>([^>]*)<\/title>/si', $head [1], $match )) {
					$result ['title'] = trim ( strip_tags ( $match [1] ) );
				}
				// 解析meta
				if (preg_match_all ( '/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $head [1], $match )) {
					// name转小写
					$names = array_map ( 'strtolower', $match [1] );
					$values = $match [2];
					$limiti = count ( $names );
					for($i = 0; $i < $limiti; $i ++) {
						$result ['metaTags'] [$names [$i]] = $values [$i];
					}
				}
				if (isset ( $result ['metaTags'] ['keywords'] )) {//将关键词切成数组
					$keywords = str_replace ( [ '，','|','、',' ' ], ',', $result ['metaTags'] ['keywords'] );
					$result ['keywords'] = explode ( ',', $keywords );
				}
			}
		}
		return $result;
	}

	/**
	 * 解析Cookie字符串
	 *
	 * @param string $cookie
	 */
	private function resolveCookie($cookie)
	{
		if (($pos = strpos ( $cookie, ';' )) !== false) {
			list ( $name, $value ) = explode ( '=', substr ( $cookie, 0, $pos ), 2 );
			$this->cookies [$name] = $value;
		}
	}

	/**
	 * 魔术方法，输出内容
	 *
	 * @return string
	 */
	public function __toString()
	{
		return ( string ) $this->getContent ();
	}
}
