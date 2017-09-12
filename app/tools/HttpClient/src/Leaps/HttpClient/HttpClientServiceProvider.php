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

use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app ['httpclient'] = $this->app->share ( function ($app)
		{
			if (function_exists ( "curl_init" )) {
				$className = "\\Leaps\\HttpClient\\Adapter\\Curl";
			} else {
				$className = "\\Leaps\\HttpClient\\Adapter\\Fsock";
			}
			return new $className ();
		} );
	}
}
