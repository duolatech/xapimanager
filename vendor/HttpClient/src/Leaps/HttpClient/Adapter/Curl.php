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

class Curl extends \Leaps\HttpClient\Adapter implements \Leaps\HttpClient\AdapterInterface
{

	/**
	 * 设置认证帐户和密码
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function setAuthorization($username, $password)
	{
		$this->authorizationToken = "[$username]:[$password]";
	}

	/**
	 * 设置curl参数
	 *
	 * @param string $key
	 * @param value $value
	 * @return \Leaps\HttpClient\Adapter\Curl
	 */
	public function setOption($key, $value)
	{
		if ($key === CURLOPT_HTTPHEADER) {
			$this->header = array_merge ( $this->header, $value );
		} else {
			$this->option [$key] = $value;
		}
		return $this;
	}

	/**
	 * 添加上传文件
	 *
	 * @param $file_name string 文件路径
	 * @param $name string 文件名
	 * @return $this
	 */
	public function _addFile($name, $fileName = '', $mimeType = '')
	{
		if (class_exists ( '\CURLFile' )) {
			$this->files [$name] = new \CURLFile ( realpath ( $fileName ), MimeType::getMimeType ( $fileName ), basename ( $fileName ) );
			$this->setOption ( CURLOPT_SAFE_UPLOAD, true );
		} else {
			$this->files [$name] = '@' . realpath ( $fileName );
		}
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
		if ($this->method == 'POST') {
			$this->setOption ( CURLOPT_POST, true );
		} else if ($this->method == 'PUT') {
			$this->setOption ( CURLOPT_PUT, true );
		} else if ($this->method) {
			$this->setOption ( CURLOPT_CUSTOMREQUEST, $this->method );
		}
		if (is_array ( $url )) {
			$data = $this->requestUrl ( $url );
			$this->reset ();
			return $data;
		} else {
			$data = $this->requestUrl ( [ $url ] );
			$this->reset ();
			return $data; // [$url];
		}
	}

	/**
	 * 用POST方式提交，支持多个URL $urls = array ( 'http://www.baidu.com/',
	 * 'http://mytest.com/url',
	 * 'http://www.abc.com/post', ); $data = array (
	 * array('k1'=>'v1','k2'=>'v2'),
	 * array('a'=>1,'b'=>2), 'aa=1&bb=3&cc=3', );
	 *
	 * @param $url
	 * @param string/array $vars
	 * @return string, false on failure
	 */
	public function postRequest($url, $vars)
	{
		// POST模式
		$this->setMethod ( 'POST' );
		$this->setOption ( CURLOPT_HTTPHEADER, [ 'Expect:' ] );
		if (is_array ( $url )) {
			$myVars = [ ];
			foreach ( $url as $k => $u ) {
				if (isset ( $vars [$k] )) {
					if (! is_array ( $vars [$k] )) {
						parse_str ( $vars [$k], $tmp );
						$vars [$k] = $tmp;
					}
					$myVars [$u] = $vars [$k];
					if ($this->files) {
						$myVars [$u] = array_merge ( $myVars [$u], $this->files );
					}
				}
			}
		} else {
			if (! is_array ( $vars )) {
				parse_str ( $vars, $tmp );
				$vars = $tmp;
			}
			if ($this->files) {
				$vars = array_merge ( $vars, $this->files );
			}
			$myVars [$url] = $vars;
		}
		$this->postData = $myVars;
		return $this->getRequest ( $url );
	}

	/**
	 * PUT方式获取数据，支持多个URL
	 *
	 * @param string/array $url
	 * @param string/array $vars
	 * @return string, false on failure
	 */
	public function putRequest($url, $vars)
	{
		$this->setMethod ( 'PUT' );
		$this->setOption ( CURLOPT_HTTPHEADER, [ 'Expect:' ] );
		if (is_array ( $url )) {
			$myvars = [ ];
			foreach ( $url as $k => $u ) {
				if (isset ( $vars [$k] )) {
					if (! is_array ( $vars [$k] )) {
						parse_str ( $vars [$k], $tmp );
						$vars [$k] = $tmp;
					}
					$myvars [$u] = $vars [$k];
				}
			}
		} else {
			if (! is_array ( $vars )) {
				parse_str ( $vars, $tmp );
				$vars = $tmp;
			}
			$myvars [$url] = $vars;
		}
		$this->postData = $myvars;
		return $this->getRequest ( $url );
	}

	/**
	 * DELETE方式获取数据，支持多个URL
	 *
	 * @param string/array $url
	 * @return string, false on failure
	 */
	public function deleteRequest($url)
	{
		$this->setMethod ( 'DELETE' );
		$this->getRequest ( $url );
	}

	/**
	 * 创建一个CURL对象
	 *
	 * @param string $url URL地址
	 * @param int $timeout 超时时间
	 * @return curl_init()
	 */
	public function _create($url)
	{
		$matches = parse_url ( $url );
		$host = $matches ['host'];
		if ($this->hostIp) {
			$this->header [] = 'Host: ' . $host;
			$url = str_replace ( $host, $this->hostIp, $url );
		}
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, true );
		// 抓取跳转后的页面
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_ENCODING, 'gzip, deflate' );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->timeout );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout );
		if (! is_null ( $this->authorizationToken )) { // 认证
			curl_setopt ( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt ( $ch, CURLOPT_USERPWD, $this->authorizationToken );
		}
		if ($matches ['scheme'] == 'https') {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		}
		if (! empty ( $this->proxyHost ) && ! empty ( $this->proxyPort )) {
			curl_setopt ( $ch, CURLOPT_PROXY, $this->proxyHost );
			curl_setopt ( $ch, CURLOPT_PROXYPORT, $this->proxyPort );
		}
		if($this->cookieJar != false && is_string($this->cookieJar)){
			curl_setopt ( $ch, CURLOPT_COOKIEJAR, $this->cookieJar );
		} else if($this->cookie){
			if (is_array ( $this->cookie )) {
				curl_setopt ( $ch, CURLOPT_COOKIE, http_build_query ( $this->cookie, '', ';' ) );
			} else {
				curl_setopt ( $ch, CURLOPT_COOKIE, $this->cookie );
			}
		}

		if ($this->referer) {
			curl_setopt ( $ch, CURLOPT_REFERER, $this->referer );
		} else {
			curl_setopt ( $ch, CURLOPT_AUTOREFERER, true );
		}
		if ($this->userAgent) {
			curl_setopt ( $ch, CURLOPT_USERAGENT, $this->userAgent );
		} elseif (array_key_exists ( 'HTTP_USER_AGENT', $_SERVER )) {
			curl_setopt ( $ch, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] );
		} else {
			curl_setopt ( $ch, CURLOPT_USERAGENT, "PHP/" . PHP_VERSION . " HttpClient/1.2.5" );
		}

		foreach ( $this->option as $k => $v ) {
			curl_setopt ( $ch, $k, $v );
		}

		if ($this->header) {
			$header = [ ];
			foreach ( $this->header as $item ) {
				// 防止有重复的header
				if (preg_match ( '#(^[^:]*):.*$#', $item, $m )) {
					$header [$m [1]] = $item;
				}
			}
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, array_values ( $header ) );
		}
		// 设置POST数据
		if (isset ( $this->postData [$url] )) {
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, is_array ( $this->postData [$url] ) ? http_build_query ( $this->postData [$url] ) : $this->postData [$url] );
		}
		return $ch;
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
		$mh = curl_multi_init ();
		// 监听列表
		$listenerList = [ ];
		// 返回值
		$result = [ ];
		// 总列队数
		$listNum = 0;
		// 排队列表
		$multiList = [ ];
		// 记录页面跳转数据
		$redirectList = [ ];
		foreach ( $urls as $url ) {
			// 创建一个curl对象
			$current = $this->_create ( $url );
			if ($this->multiExecNum > 0 && $listNum >= $this->multiExecNum) {
				// 加入排队列表
				$multiList [] = $url;
			} else {
				// 列队数控制
				curl_multi_add_handle ( $mh, $current );
				$listenerList [$url] = $current;
				$listNum ++;
			}
			$result [$url] = null;
			$this->httpData [$url] = null;
		}
		unset ( $current );
		$running = null;
		// 已完成数
		$doneNum = 0;
		do {
			while ( ($execrun = curl_multi_exec ( $mh, $running )) == CURLM_CALL_MULTI_PERFORM )
				;
			if ($execrun != CURLM_OK) {
				break;
			}
			while ( true == ($done = curl_multi_info_read ( $mh )) ) {
				foreach ( $listenerList as $doneUrl => $listener ) {
					if ($listener === $done ['handle']) {
						// 获取内容
						$this->httpData [$doneUrl] = $this->getData ( curl_multi_getcontent ( $done ['handle'] ), $done ['handle'] );
						// 判断有无跳转 在CURL自动跳转失败时有效
						if (preg_match ( '#Location(?:[ ]*):([^\r]+)\r\n#Uis', $this->httpData [$doneUrl] ['rawHeader'], $m )) {
							if (isset ( $redirectList [$doneUrl] ) && count ( $redirectList [$doneUrl] ) >= 10) { // 超过最大跳转次数
								$result [$doneUrl] = false;
							} else {
								curl_close ( $done ['handle'] );
								curl_multi_remove_handle ( $mh, $done ['handle'] );
								$newUrl = trim ( $m [1] );
								if (strpos ( $newUrl, "://" ) == false) {
									$newUrl = $doneUrl . $newUrl;
								}
								// 跳转计数
								$redirectList [$doneUrl] [] = $newUrl;
								// 把Cookie弄过去
								if (preg_match ( '#Set-Cookie(?:[ ]*):([^\r+])\r\n#is', $this->httpData [$doneUrl] ['rawHeader'], $m2 )) {
									// 把cookie传递过去
									$oldCookie = $this->cookie;
									$this->cookie = $m2 [1];
								}
								// 创建CURL对象
								$current = $this->_create ( $newUrl );
								// 加入到列队
								curl_multi_add_handle ( $mh, $current );
								// 更新监听列队信息
								$listenerList [$doneUrl] = $current;
								if (isset ( $oldCookie )) {
									$this->cookie = $oldCookie;
								}
								continue;
							}
						} else if ($this->httpData [$doneUrl] ['code'] != 200) {
							// \Leaps\Debug::error ( 'URL:' . $doneUrl . ' ERROR,TIME:' . $this->httpData [$doneUrl] ['time'] . ',CODE:' . $this->httpData [$doneUrl] ['code'] );
							$result [$doneUrl] = false;
						} else {
							// 返回内容
							$result [$doneUrl] = $this->httpData [$doneUrl] ['data'];
							// \Leaps\Debug::info ( 'URL:' . $doneUrl . ' OK.TIME:' . $this->httpData [$doneUrl] ['time'] );
						}
						curl_close ( $done ['handle'] );
						curl_multi_remove_handle ( $mh, $done ['handle'] );
						// 把监听列表里移除
						unset ( $listenerList [$doneUrl], $listener );
						$doneNum ++;
						// 如果还有排队列表，则继续加入
						if ($multiList) {
							// 获取列队中的一条URL
							$currentUrl = array_shift ( $multiList );
							// 创建CURL对象
							$current = $this->_create ( $currentUrl );
							// 加入到列队
							curl_multi_add_handle ( $mh, $current );
							// 更新监听列队信息
							$listenerList [$currentUrl] = $current;
							unset ( $current );
							// 更新列队数
							$listNum ++;
						}
						break;
					}
				}
			}
			if ($doneNum >= $listNum)
				break;
		} while ( true );
		// 关闭列队
		curl_multi_close ( $mh );
		return $result;
	}

	/**
	 * 获取数据
	 *
	 * @param unknown $data
	 * @param unknown $ch
	 * @return mixed
	 */
	protected function getData($data, $ch)
	{
		$headerSize = curl_getinfo ( $ch, CURLINFO_HEADER_SIZE );
		$result ['code'] = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		$result ['data'] = substr ( $data, $headerSize );
		$result ['rawHeader'] = substr ( $data, 0, $headerSize );
		$result ['header'] = explode ( "\r\n", $result ['rawHeader'] );
		$result ['time'] = curl_getinfo ( $ch, CURLINFO_TOTAL_TIME );
		return $result;
	}
}
