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
namespace LeapsTest;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
	public $testUrl = 'http://127.0.0.1/httpclient/tests/test.php';
	public $testUrls = [
			'http://127.0.0.1/httpclient/tests/test.php',
			'http://127.0.0.1/httpclient/tests/test2.php'
	];
	public $postVar = [
			'aaa' => 'bbb'
	];
	public $postVars = [
			[
					'vv' => 'cc'
			],
			[
					'cc' => 'ss'
			]
	];
	public function testCurlGet()
	{
		$http = new \Leaps\HttpClient\Adapter\Curl ();
		$response = $http->get ( $this->testUrl );
		$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $response );
	}
	public function testCurlGets()
	{
		$http = new \Leaps\HttpClient\Adapter\Curl ();
		$response = $http->get ( $this->testUrls );
		if (is_array ( $response )) {
			foreach ( $response as $r ) {
				$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $r );
			}
		}
	}
	public function testFsockGet()
	{
		$http = new \Leaps\HttpClient\Adapter\Fsock ();
		$response = $http->get ( $this->testUrl );
		$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $response );
	}
	public function testFsockGets()
	{
		$http = new \Leaps\HttpClient\Adapter\Fsock ();
		$response = $http->get ( $this->testUrls );
		if (is_array ( $response )) {
			foreach ( $response as $r ) {
				$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $r );
			}
		}
	}
	public function testCurlPost()
	{
		$http = new \Leaps\HttpClient\Adapter\Curl ();
		$response = $http->post ( $this->testUrl, $this->postVar );
		$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $response );
	}
	public function testCurlPosts()
	{
		$http = new \Leaps\HttpClient\Adapter\Curl ();
		$response = $http->post ( $this->testUrls, $this->postVars );
		if (is_array ( $response )) {
			foreach ( $response as $r ) {
				$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $r );
			}
		}
	}
	public function testFsockPost()
	{
		$http = new \Leaps\HttpClient\Adapter\Fsock ();
		$response = $http->get ( $this->testUrl, $this->postVar );
		$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $response );
	}
	public function testFsockPosts()
	{
		$http = new \Leaps\HttpClient\Adapter\Fsock ();
		$response = $http->get ( $this->testUrls, $this->postVars );
		if (is_array ( $response )) {
			foreach ( $response as $r ) {
				$this->assertInstanceOf ( '\Leaps\HttpClient\Response', $r );
			}
		}
	}
}