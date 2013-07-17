<?php
/**
 * eyes扩展
 */

require_once 'Zend/Http/Client.php';

class Zend_Http_Client2 extends Zend_Http_Client {
	
	private $_requestUrl;
	private $_response;
	public $autoReferer = true;

	public function __construct($uri = null, $config = null)
	{
		$this->setConfig(array('useragent' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)'));
		parent::__construct($uri, $config);
		$this->setCookieJar();
	}
	
	public function get($url)
	{
		$this->_requestUrl = $url;
		$this->setUri($url);
		$this->_response = $this->request();
		return $this->_response->getStatus();
	} //get
	
	public function post($url,$data)
	{
		$this->_requestUrl = $url;
		$this->setUri($url);
		$this->setParameterPost($data);
		$this->_response = $this->request(self::POST);
		return $this->_response->getStatus();
	} //post
	
	public function getBody()
	{
		return $this->_response->getBody();
	} //body
	
	public function getHeaders()
	{
		return $this->_response->getHeaders();
	} //headers
	
	public function getHtml($url)
	{
		$this->get($url);
		return $this->getBody();
	} //html
	
	public function request($method = null)
	{
		if ($this->autoReferer && $this->getHeader('referer')==null) {
			$this->setHeaders('referer', $this->_requestUrl);
		}
		return parent::request($method);
	} //request
	
	/**
	 * 获取所有cookie对象
	 *
	 * @return object
	 */
	public function getCookies()
	{
		return $this->getCookieJar()->getAllCookies();
	} //getCookies
	
	/**
	 * 设置cookie对象
	 *
	 * @param array $cookies
	 */
	public function addCookies($cookies)
	{
		foreach ((array)$cookies as $cookie) {
			$this->getCookieJar()->addCookie($cookie);
		}
	} //addCookies
	
	
}