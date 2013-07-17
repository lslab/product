<?php
set_time_limit(0);

Doo::loadModel('Img');
Doo::loadModel('Thread');

Doo::loadClass('functions');
Doo::loadClass('curl');
Doo::loadClass('simple_html_dom');

define('R',Doo::conf()->SITE_PATH);

class BaseController extends DooController{

	public $http;
	public $pinyins=array (
	'美食' => 'meishi',
	'咖啡厅' => 'kafeiting',
	'酒吧' => 'jiuba',
	'茶馆' => 'chaguan',
	'宾馆酒店' => 'binguanjiudian',
	'洗浴' => 'xiyu',
	'足疗' => 'zuliao',
	'按摩' => 'anmo',
	'KTV' => 'ktv',
	'夜总会' => 'yezonghui',
	'娱乐城' => 'yulecheng',
	'舞厅' => 'wuting',
	);

	public function __construct()
	{
		set_time_limit(0);
	}//__construct(

	/**
	 * httpclient
	 *
	 * @return HttpClient
	 */
	public function http()
	{
		if (!$this->http) {
			ini_set('include_path', dirname(__FILE__) . '/../class/PEAR');
			include_once('HTTP/myClient.php');

			$http = new myHttpClient();
			$httpHeader = array(
			//'Referer' => 'http://www.txtcn.cn/',
			'Accept-Language' => 'zh-cn',
			'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
			);
			$http->setDefaultHeader($httpHeader);
			$http->setMaxRedirects(0);
			$this->http=$http;
			//			$this->http=new Curl();
		}
		return $this->http;
	}//http

	public function html($url)
	{
		return $this->http()->get($url);

		$id=$url;
		if (!$s=Doo::cache()->get($id)) {
			$s=$this->http()->get($url);
			Doo::cache()->set($id,$s);
		}
		return $s;
	}

	public function cacheGet($url)
	{
		$id=$url;
		if (!$s=Doo::cache()->get($id)) {
			$s=$this->http()->get($url);
			Doo::cache()->set($id,$s);
		}
		return $s;
	}

	public function after()
	{
		echo "\n<br>Finish\n";
	}//after

}
?>