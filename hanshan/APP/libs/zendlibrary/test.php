<?php
ini_set('include_path', dirname(__FILE__) . '/../zendlibrary');

include('Zend/Http/Client2.php');
$http=new Zend_Http_Client2();

$http->setConfig(
	array(
		'maxredirects' => 0,
		'timeout'      => 5,
		'adapter'   => 'Zend_Http_Client_Adapter_Curl',
		'proxy_host' => '127.0.0.1',
		'proxy_port' => 9050,
		'curloptions' => array(
			CURLOPT_PROXYTYPE 	=> CURLPROXY_SOCKS5,
//			CURLOPT_PROXY		=>	'127.0.0.1',
//			CURLOPT_PROXYPORT	=>	9050,
		),
	)
);

$http->setHeaders('Referer','http://www.baidu.com/99');

echo $html=$http->gethtml('http://www.cmyip.com');
