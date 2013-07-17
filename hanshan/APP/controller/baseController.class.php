<?php

class baseController extends Controller {

	/**
	 * Zend_Http_Client2
	 *
	 * @var Zend_Http_Client2
	 */
	public $http;

	public function __construct()
	{
		parent::__construct();
		
		set_include_path(APP_PATH . '/libs/zendlibrary');

		include('Zend/Http/Client2.php');
		$this->http=new Zend_Http_Client2();

		$this->http->setConfig(
			array(
				'maxredirects' => 0,
				'timeout'      => 5,
				'adapter'   => 'Zend_Http_Client_Adapter_Curl',
//				'proxy_host' => '127.0.0.1',
//				'proxy_port' => 8580,
//				'curloptions' => array(
//					CURLOPT_PROXYTYPE 	=> CURLPROXY_SOCKS5,
//								CURLOPT_PROXY		=>	'127.0.0.1',
//								CURLOPT_PROXYPORT	=>	8580,
//				),
			)
		);

		//$http->setHeaders('Referer','http://www.baidu.com/99');
	}
}


?>