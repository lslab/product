<?php
ini_set('include_path', dirname(__FILE__) . '/../zendlibrary');

include('Zend/Http/Client2.php');
$http=new Zend_Http_Client2();

$http->setConfig(
	array(
		'maxredirects' => 0,
		'useragent'	=>	'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
		'timeout'      => 5,
		'adapter'   => 'Zend_Http_Client_Adapter_Curl',
		//'proxy_host' => '127.0.0.1',
		//'proxy_port' => 9050,
//		'curloptions' => array(
//			CURLOPT_PROXYTYPE 	=> CURLPROXY_SOCKS5,
//			CURLOPT_PROXY		=>	'127.0.0.1',
//			CURLOPT_PROXYPORT	=>	9050,
//		),
	)
);
		
			$postData=array(
		'username'	=>	'xiaohd8',
		'pwd'	=>	'pass1234',
		'button'	=>	'µÇ Â¼'
		);
		$code=$http->post('http://www.u7077.com/logincheck.asp',$postData);
		var_dump($code);
		var_dump($http->getHeaders());
		var_dump($http->getBody());