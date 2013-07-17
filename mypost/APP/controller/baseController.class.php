<?php
//include(APP_PATH.'/class/curl.php');
include(APP_PATH.'/class/Spreadsheet_Excel_Reader.php');

class baseController extends Controller {

	/**
	 * HttpClient
	 *
	 * @var HttpClient
	 */
	public $http;

	public function __construct()
	{
		parent::__construct();
		
//		$this->http = new Curl();
		$this->http = new HttpClient();

		//		$this->http=new Curl();
//		ini_set('include_path', dirname(__FILE__) . '/../class/PEAR');
//		include_once('HTTP/myClient.php');
//
//		$http = new myHttpClient();
//		$httpHeader = array(
//		//'Referer' => 'http://www.txtcn.cn/',
//		'Accept-Language' => 'zh-cn',
//		'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
//		);
//		$http->setDefaultHeader($httpHeader);
//		$http->setMaxRedirects(0);
//		$this->http=$http;
	}//__construct(


}

