<?php

class baseController extends Controller {

	public $charset='utf-8';

	/**
	 * siteModel
	 *
	 * @var siteModel
	 */
	public $site;
	
	/**
	 * resumeModel
	 *
	 * @var resumeModel
	 */
	public $resume;

	/**
	 * Zend_Http_Client2
	 *
	 * @var Zend_Http_Client2
	 */
	public $http;

	public function __construct()
	{
		parent::__construct();

		ini_set('include_path', APP_PATH . '/lib/zendlibrary');

		include('Zend/Http/Client2.php');
		$this->http=new Zend_Http_Client2();

		$this->http->setConfig(
		array(
		'maxredirects' => 0,
		'useragent'	=>	'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
		'timeout'      => 5,
		'adapter'   => 'Zend_Http_Client_Adapter_Curl',
		)
		);
		
		//初始化模型
		$this->site=new siteModel();
		$this->resume=new resumeModel();
		
		//注册自定义类
		$this->regClassFile('resumeRegex', APP_PATH.'/lib/resumeRegex.php');
	}//__construct(


	public function get($url)
	{
		if ($this->charset=='gbk') {
			return $this->http->getHtml($url);
		} else {
			return $this->http->gbkHtml($url);
		}
	}//get

	public function body()
	{
		if ($this->charset=='gbk') {
			return $this->http->getBody();
		} else {
			return $this->http->gbkBody();
		}
	}//body
	
}


?>