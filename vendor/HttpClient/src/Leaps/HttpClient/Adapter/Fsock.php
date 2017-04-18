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
namespace Leaps\HttpClient\Adapter;

use Leaps\HttpClient\MimeType;

class Fsock extends \Leaps\HttpClient\Adapter implements \Leaps\HttpClient\AdapterInterface
{
	/**
	 * 设置认证帐户和密码
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function setAuthorization($username, $password)
	{
		$this->authorizationToken = " Basic " . base64_encode ( $username . ":" . $password );
	}

	/**
	 * 设置参数
	 *
	 * @param string $key
	 * @param string $value
	 * @return \Leaps\HttpClient\Adapter\Fsock
	 */
	public function setOption($key, $value)
	{
		return $this;
	}

	/**
	 * 添加上次文件
	 *
	 * @param $file_name string 文件路径
	 * @param $name string 文件名
	 * @return $this
	 */
	public function _addFile($name, $fileName, $mimeType = '')
	{
		$this->files [$name] = $fileName;
		return $this;
	}

	/**
	 * GET方式获取数据，支持多个URL
	 *
	 * @param string/array $url
	 * @return string, false on failure
	 */
	public function getRequest($url)
	{
		if (is_array ( $url )) {
			$data = $this->requestUrl ( $url );
			$this->reset ();
		} else {
			$data = $this->requestUrl ( [ $url ] );
			$this->reset ();
		}
		return $data;
	}

	/**
	 * 用POST方式提交，支持多个URL $urls = array ( 'http://www.baidu.com/',
	 * 'http://mytest.com/url',
	 * 'http://www.abc.com/post', ); $data = array (
	 * array('k1'=>'v1','k2'=>'v2'),
	 * array('a'=>1,'b'=>2), 'aa=1&bb=3&cc=3', );
	 * HttpClient::factory()->post($url,$data);
	 *
	 * @param $url
	 * @param string/array $vars
	 * @param $timeout 超时时间，默认120秒
	 * @return string, false on failure
	 */
	public function postRequest($url, $vars)
	{
		// POST模式
		$this->setMethod ( 'POST' );
		if (is_array ( $url )) {
			$myVars = [ ];
			foreach ( $url as $k => $u ) {
				if (isset ( $vars [$k] )) {
					if (is_array ( $vars [$k] )) {
						if ($this->files) {
							// 如果需要上传文件，则不需要预先将数组转换成字符串
							$myVars [$u] = $vars [$k];
						} else {
							$myVars [$u] = http_build_query ( $vars [$k] );
						}
					} else {
						$myVars [$u] = $vars [$k];
					}
				}
			}
		} else {
			$myVars = [ $url => $vars ];
		}
		$this->postData = $myVars;
		return $this->getRequest ( $url );
	}

	/**
	 * PUT方式获取数据，支持多个URL
	 *
	 * @param string/array $url
	 * @param string/array $vars
	 * @param $timeout
	 * @return string, false on failure
	 */
	public function putRequest($url, $vars)
	{
		$this->setMethod ( 'PUT' );
		$this->contentType = "application/x-www-form-urlencoded";
		if (is_array ( $url )) {
			$myvars = [ ];
			foreach ( $url as $k => $u ) {
				if (isset ( $vars [$k] )) {
					if (is_array ( $vars [$k] )) {
						$myvars [$u] = http_build_query ( $vars [$k] );
					} else {
						$myvars [$u] = $vars [$k];
					}
				}
			}
		} else {
			$myvars = [ $url => $vars ];
		}
		$this->postData = $myvars;

		return $this->getRequest ( $url );
	}

	/**
	 * DELETE方式获取数据，支持多个URL
	 *
	 * @param string/array $url
	 * @param $timeout
	 * @return string, false on failure
	 */
	public function deleteRequest($url)
	{
		$this->setMethod ( 'DELETE' );
		return $this->getRequest ( $url );
	}

	/**
	 * 创建一个CURL对象
	 *
	 * @param string $url URL地址
	 * @param int $timeout 超时时间
	 * @return fsockopen()
	 */
	public function _create($url)
	{
		$matches = parse_url ( $url );
		$hostname = $matches ['host'];
		$uri = isset ( $matches ['path'] ) ? $matches ['path'] . (isset ( $matches ['query'] ) ? '?' . $matches ['query'] : '') : '/';
		$connPort = isset ( $matches ['port'] ) ? intval ( $matches ['port'] ) : ($matches ['scheme'] == 'https' ? 443 : 80);
		if ($matches ['scheme'] == 'https') {
			$connHost = $this->hostIp ? 'tls://' . $this->hostIp : 'tls://' . $hostname;
		} else {
			$connHost = $this->hostIp ? $this->hostIp : $hostname;
		}

		$header = [ 'Host' => $hostname,'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8','Accept-Encoding' => 'gzip, deflate','Connection' => 'Close' ];
		if (! is_null ( $this->authorizationToken )) { // 认证
			$header ['Authorization'] = $this->authorizationToken;
		}

		if ($this->userAgent) {
			$header ['User-Agent'] = $this->userAgent;
		} elseif (array_key_exists ( 'HTTP_USER_AGENT', $_SERVER )) {
			$header ['User-Agent'] = $_SERVER ['HTTP_USER_AGENT'];
		} else {
			$header ['User-Agent'] = "PHP/" . PHP_VERSION . " HttpClient/1.4.7";
		}
		if ($this->referer) {
			$header ['Referer'] = $this->referer;
		}
		if ($this->cookie) {
			$header ['Cookie'] = is_array ( $this->cookie ) ? http_build_query ( $this->cookie, '', ';' ) : $this->cookie;
		}
		if ($this->header) {
			foreach ( $this->header as $item ) {
				// 防止有重复的header
				if (preg_match ( '#(^[^:]*):(.*)$#', $item, $m )) {
					$header [trim ( $m [1] )] = trim ( $m [2] );
				}
			}
		}
		if ($this->files) {
			$boundary = '----------------------------' . substr ( md5 ( microtime ( 1 ) . mt_rand () ), 0, 12 );
			$vars = "--$boundary\r\n";
			if ($this->postData [$url]) {
				if (! is_array ( $this->postData [$url] )) {
					parse_str ( $this->postData [$url], $post );
				} else {
					$post = $this->postData [$url];
				}
				// form data
				foreach ( $post as $key => $val ) {
					$vars .= "Content-Disposition: form-data; name=\"" . rawurlencode ( $key ) . "\"\r\n";
					$vars .= "Content-type:application/x-www-form-urlencoded\r\n\r\n";
					$vars .= rawurlencode ( $val ) . "\r\n";
					$vars .= "--$boundary\r\n";
				}
			}
			foreach ( $this->files as $name => $filename ) {
				$vars .= "Content-Disposition: form-data; name=\"" . $name . "\"; filename=\"" . rawurlencode ( basename ( $filename ) ) . "\"\r\n";
				$vars .= "Content-Type: " . MimeType::getMimeType ( $filename ) . "\r\n\r\n";
				$vars .= file_get_contents ( $filename ) . "\r\n";
				$vars .= "--$boundary\r\n";
			}
			$vars .= "--\r\n\r\n";
			$header ['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
		} else if (isset ( $this->postData [$url] ) && $this->postData [$url]) {
			// 设置POST数据
			$vars = is_array ( $this->postData [$url] ) ? http_build_query ( $this->postData [$url] ) : ( string ) $this->postData [$url];
			$header ['Content-Type'] = 'application/x-www-form-urlencoded';
		} else {
			$vars = '';
		}
		// 设置长度
		$header ['Content-Length'] = strlen ( $vars );
		if (! is_null ( $this->proxyHost ) && ! is_null ( $this->proxyPort )) {
			$connHost = $this->proxyHost;
			$connPort = $this->proxyPort;
			$str = $this->method . ' ' . $url . ' HTTP/1.1' . "\r\n";
		} else {
			$str = $this->method . ' ' . $uri . ' HTTP/1.1' . "\r\n";
		}
		foreach ( $header as $k => $v ) {
			$str .= $k . ': ' . str_replace ( [ "\r","\n" ], '', $v ) . "\r\n";
		}
		$str .= "\r\n";
		if ($this->timeout > ini_get ( 'max_execution_time' )) {
			@set_time_limit ( $this->timeout );
		}
		$ch = @fsockopen ( $connHost, $connPort, $errno, $errstr, $this->timeout );
		if (! $ch) {
			// \Leaps\Debug::error ( "$errstr ($errno)" );
			return false;
		} else {
			stream_set_blocking ( $ch, TRUE );
			fwrite ( $ch, $str );
			if ($vars) {
				// 追加POST数据
				fwrite ( $ch, $vars );
			}
			return $ch;
		}
	}

	/**
	 * 支持多线程获取网页
	 *
	 * @see http://cn.php.net/manual/en/function.curl-multi-exec.php#88453
	 * @param Array/string $urls
	 * @param Int $timeout
	 * @return Array
	 */
	public function requestUrl($urls)
	{
		// 去重
		$urls = array_unique ( $urls );
		if (! $urls)
			return [ ];
			// 监听列表
		$listenerList = [ ];
		// 返回值
		$result = [ ];
		// 总列队数
		$listNum = 0;
		// 记录页面跳转数据
		$redirectList = [ ];
		// 排队列表
		$multiList = [ ];
		foreach ( $urls as $url ) {
			if ($this->multiExecNum > 0 && $listNum >= $this->multiExecNum) {
				// 加入排队列表
				$multiList [] = $url;
			} else {
				// 列队数控制
				$listenerList [] = [ $url,$this->_create ( $url ) ];
				$listNum ++;
			}
			$result [$url] = null;
			$this->httpData [$url] = null;
		}
		// 已完成数
		$doneNum = 0;
		while ( $listenerList ) {
			list ( $doneUrl, $ch ) = array_shift ( $listenerList );
			$time = microtime ( 1 );
			if (! $ch) {
				$result [$doneUrl] = null;
				continue;
			}
			$str = '';
			while ( true ) {
				if (feof ( $ch )) {
					break;
				}
				$str .= fgets ( $ch, 4096 );
			}
			fclose ( $ch );
			$time = microtime ( 1 ) - $time;
			list ( $header, $body ) = explode ( "\r\n\r\n", $str, 2 );
			$headerArr = explode ( "\r\n", $header );
			$firstLine = array_shift ( $headerArr );
			if (preg_match ( '#^HTTP/1.1 ([0-9]+) #', $firstLine, $m )) {
				$code = $m [1];
			} else {
				$code = 0;
			}
			if (strpos ( $header, 'Transfer-Encoding: chunked' )) {
				$body = preg_replace_callback ( '/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)' . '((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si', create_function ( '$matches', 'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];' ), $body );
			}
			if (preg_match ( '#Location(?:[ ]*):([^\r]+)\r\n#Uis', $header, $m )) {
				if (isset ( $redirectList [$doneUrl] ) && count ( $redirectList [$doneUrl] ) >= 10) {
					// 防止跳转次数太大
					$body = $header = '';
					$code = 0;
				} else {
					// 302 跳转
					$newUrl = trim ( $m [1] );
					$redirectList [$doneUrl] [] = $newUrl;
					// 插入列队
					if (preg_match ( '#Set-Cookie(?:[ ]*):([^\r+])\r\n#is', $header, $m2 )) {
						// 把cookie传递过去
						$oldCookie = $this->cookie;
						$this->cookie = $m2 [1];
					}
					array_unshift ( $listenerList, [ $doneUrl,$this->_create ( $newUrl ) ] );
					if (isset ( $oldCookie )) {
						$this->cookie = $oldCookie;
					}
					continue;
				}
			}

			if (strpos ( $header, 'Content-Encoding: gzip' )) {
				$body = gzdecode ( $body );
			}

			$rs = [ 'code' => $code,'data' => $body,'rawHeader' => $header,'header' => $headerArr,'time' => $time ];
			$this->httpData [$doneUrl] = $rs;
			if ($rs ['code'] != 200) {
				// \Leaps\Debug::error ( 'URL:' . $doneUrl . ' ERROR,TIME:' . $this->httpData [$doneUrl] ['time'] . ',CODE:' . $this->httpData [$doneUrl] ['code'] );
				$result [$doneUrl] = false;
			} else {
				// \Leaps\Debug::info ( 'URL:' . $doneUrl . ' OK.TIME:' . $this->httpData [$doneUrl] ['time'] );
				$result [$doneUrl] = $rs ['data'];
			}
			$doneNum ++;
			if ($multiList) {
				// 获取列队中的一条URL
				$currentUrl = array_shift ( $multiList );
				// 更新监听列队信息
				$listenerList [] = [ $currentUrl,$this->_create ( $currentUrl ) ];
				// 更新列队数
				$listNum ++;
			}
			if ($doneNum >= $listNum)
				break;
		}
		return $result;
	}
}
