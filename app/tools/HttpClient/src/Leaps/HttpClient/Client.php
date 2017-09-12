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

class Client
{

	/**
	 * Http驱动实例
	 *
	 * @var \Leaps\HttpClient\AdapterInterface
	 */
	protected $driver;

	/**
	 * 创建一个新的Http驱动实例。
	 *
	 * @param string $adapter
	 */
	public function __construct()
	{
		$reflection = new \ReflectionClass ( "\\Leaps\\HttpClient\\Adapter\\" . $this->getDefaultDriver () );
		$this->driver = $reflection->newInstance ();
	}

	/**
	 * 获取默认驱动程序名称
	 *
	 * @return Ambigous <\Leaps\mixed, mixed, array, unknown, Closure>
	 */
	public function getDefaultDriver()
	{
		if (function_exists ( "curl_init" )) {
			return "Curl";
		} else {
			return "Fsock";
		}
	}

	/**
	 * 获取适配器实例。
	 *
	 * @return \Leaps\HttpClient\AdapterInterface
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * 魔术方法，直接访问驱动的方法
	 *
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		if (method_exists ( $this->driver, $method )) {
			return call_user_func_array ( [ $this->driver,$method ], $params );
		}
	}
}