<?php
set_time_limit(0);

Doo::loadClass('functions');
Doo::loadClass('curl');
Doo::loadClass('simple_html_dom');

define('R',Doo::conf()->SITE_PATH);

class BaseController extends DooController{

	/**
	 * @var myHttpClient
	 */
	public $http;

	public function __construct()
	{
		$this->http();
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
			$s=$this->http()->html($url);
			Doo::cache()->set($id,$s);
		}
		return $s;
	}

	public function cacheGet($url)
	{
		$id=$url;
		if (!$s=Doo::cache()->get($id)) {
			$s=$this->http()->html($url);
			Doo::cache()->set($id,$s);
		}
		return $s;
	}

	public function after()
	{
		echo "\n<br>Finish\n";
	}//after

}

function getCache($id)
{
	return Doo::cache()->get($id);
}//getCache

function setCache($id,$value)
{
	return Doo::cache()->set($id,$value);
}//setCache
?>