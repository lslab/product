<?php

/**

//����ʾ��

//ʹ�ô���
$setopt = array('proxy'=>true,'proxyHost'=>'','proxyPort'=>'');
$cu = new Curl();
//�õ� baidu ����ҳ����
echo $cu->get('http://baidu.com/');

//ģ���¼
$cu->post('http://www.***.com',array('uname'=>'admin','upass'=>'admin'));
echo $cu->get('http://www.***.com');

//�ϴ����ݺ��ļ�
echo $cu->post('http://a.com/a.php',array('id'=>1,'name'=>'yuanwei'),
array('img'=>'file/a.jpg','files'=>array('file/1.zip','file/2.zip')));

//�õ����е�����Ϣ
echo 'ERRNO='.$cu->errno();
echo 'ERROR='.$cu->error();
print_r($cu->getinfo());

*/

/**
 * 
 * $Id:Curl.class.php
 * 
 * CURL HTTP������
 * 
 * ֧�����¹��ܣ�
 * 1��֧��ssl���Ӻ�proxy��������
 * 2: ��cookie���Զ�֧��
 * 3: �򵥵�GET/POST�������
 * 4: ֧�ֵ����ļ��ϴ���ͬ�ֶεĶ��ļ��ϴ�,֧�����·�������·��.
 * 5: ֧�ַ��ط�������ǰ����������еķ�������Ϣ�ͷ�����Header��Ϣ
 * 6: �Զ�֧��lighttpd������
 * 7: ֧���Զ����� REFERER ����ҳ
 * 8: �Զ�֧�ַ�����301��ת����д����
 * 9: ������ѡ��,���Զ���˿ڣ���ʱʱ�䣬USERAGENT��Gzipѹ����.
 * 
 */

class Curl{

	public $ch = null;				//CURL���
	public $info = array();		//CURLִ��ǰ�������û�������˷��ص���Ϣ

	//CURL SETOPT ��Ϣ
	private $setopt = array(
	'port'=>80,					//���ʵĶ˿�,httpĬ���� 80
	'userAgent'=>'',				//�ͻ��� USERAGENT,��:"Mozilla/4.0",Ϊ����ʹ���û��������
	'timeOut'=>30,					//���ӳ�ʱʱ��
	'useCookie'=>true,				//�Ƿ�ʹ�� COOKIE ����򿪣���Ϊһ����վ�����õ�
	'ssl'=>false,					//�Ƿ�֧��SSL
	'gzip'=>true,					//�ͻ����Ƿ�֧�� gzipѹ��

	'proxy'=>false,				//�Ƿ�ʹ�ô���
	'proxyType'=>'HTTP',			//��������,��ѡ�� HTTP �� SOCKS5
	'proxyHost'=>'123.110.89.248',	//�����������ַ
	'proxyPort'=>8909,				//���������Ķ˿�
	'proxyAuth'=>false,			//�����Ƿ�Ҫ�����֤(HTTP��ʽʱ)
	'proxyAuthType'=>'BASIC',		//��֤�ķ�ʽ.��ѡ�� BASIC �� NTLM ��ʽ
	'proxyAuthUser'=>'user',		//��֤���û���
	'proxyAuthPwd'=>'password',	//��֤������
	);

	/**
	 * ���캯��
	 *
	 * @param array $setopt :��ο� private $setopt ������
	 */
	public function Curl($setopt=array()){

		$this->setopt = array_merge($this->setopt,$setopt);		//�ϲ��û������ú�ϵͳ��Ĭ������

		function_exists('curl_init') || die('CURL Library Not Loaded');		//���û�а�װCURL����ֹ����

		$this->ch = curl_init();		//��ʼ��

		curl_setopt($this->ch, CURLOPT_PORT, $this->setopt['port']);	//����CURL���ӵĶ˿�

		//ʹ�ô���
		if($this->setopt['proxy']){
			$proxyType = strtoupper($this->setopt['proxyType'])=='HTTP' ? CURLPROXY_HTTP : CURLPROXY_SOCKS5;
			curl_setopt($this->ch, CURLOPT_PROXYTYPE, $proxyType);
			curl_setopt($this->ch, CURLOPT_PROXY, $this->setopt['proxyHost']);
			curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->setopt['proxyPort']);

			//����Ҫ��֤
			if($this->setopt['proxyAuth']){
				$proxyAuthType = $this->setopt['proxyAuthType']=='BASIC' ? CURLAUTH_BASIC : CURLAUTH_NTLM;
				curl_setopt($this->ch, CURLOPT_PROXYAUTH, $proxyAuthType);
				$user = "[{$this->setopt['proxyAuthUser']}]:[{$this->setopt['proxyAuthPwd']}]";
				curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $user);
			}
		}

		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);	//����ʱ�Ὣ���������������صġ�Location:������header�еݹ�ķ��ظ�������

		//�򿪵�֧��SSL
		if($this->setopt['ssl']){
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);	//������֤֤����Դ�ļ��
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, true);	//��֤���м��SSL�����㷨�Ƿ����
		}

		$header[]= 'Expect:';	//����httpͷ,֧��lighttpd�������ķ���
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		$userAgent = $this->setopt['userAgent'] ? $this->setopt['userAgent'] : $_SERVER['HTTP_USER_AGENT'];		//���� HTTP USERAGENT
		curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->setopt['timeOut']);	//�������ӵȴ�ʱ��,0���ȴ�
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->setopt['timeOut']);			//����curl����ִ�е������

		//���ÿͻ����Ƿ�֧�� gzipѹ��
		if($this->setopt['gzip']){
			curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip');
		}
		//�Ƿ�ʹ�õ�COOKIE
		if($this->setopt['useCookie']){
			$cookfile = tempnam(sys_get_temp_dir(),'cuk');	//���ɴ����ʱCOOKIE���ļ�(Ҫ����·��)
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookfile);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookfile);
		}
		curl_setopt($this->ch, CURLOPT_HEADER, true);			//�Ƿ�ͷ�ļ�����Ϣ��Ϊ���������(HEADER��Ϣ),���ﱣ������
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true) ;	//��ȡ����Ϣ���ļ�������ʽ���أ�������ֱ�������
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true) ;
	}

	/**
	 * �� GET ��ʽִ������
	 *
	 * @param string $url : �����URL
	 * 
	 * @param array $params ������Ĳ���,��ʽ��: array('id'=>10,'name'=>'yuanwei')
	 * 
	 * @param array $referer :����ҳ��,Ϊ��ʱ�Զ�����,����������ж�������ƵĻ���һ��Ҫ���õ�.
	 * 
	 * @return ���󷵻�:false ��ȷ����:�������
	 */
	public function get($url,$params=array(), $referer=''){
		return $this->_request('GET', $url, $params, array(), $referer);
	}

	/**
	 * �� POST ��ʽִ������
	 *
	 * @param string $url :�����URL
	 * 
	 * @param array $params ������Ĳ���,��ʽ��: array('id'=>10,'name'=>'yuanwei')
	 * 
	 * @param array $uploadFile :�ϴ����ļ�,֧�����·��,��ʽ����:
	 * �����ļ��ϴ�:array('img1'=>'./file/a.jpg'),ͬ�ֶζ���ļ��ϴ�:array('img'=>array('./file/a.jpg','./file/b.jpg'))
	 * 
	 * @param array $referer :����ҳ��,����ҳ��,Ϊ��ʱ�Զ�����,����������ж�������ƵĻ���һ��Ҫ���õ�.
	 * 
	 * @return ���󷵻�:false ��ȷ����:�������
	 */
	public function post($url,$params=array(),$uploadFile=array(), $referer=''){
		return $this->_request('POST', $url, $params, $uploadFile, $referer);
	}

	/**
	 * �õ�������Ϣ
	 *
	 * @return string
	 */
	public function error(){
		return curl_error($this->ch);
	}

	/**
	 * �õ��������
	 *
	 * @return int
	 */
	public function errno(){
		return curl_errno($this->ch);
	}

	/**
	 * �õ���������ǰ����������еķ�������Ϣ�ͷ�����Header��Ϣ:
	 * [before] ������ǰ�����õ���Ϣ
	 * [after] :��������еķ�������Ϣ
	 * [header] :������Header������Ϣ
	 *
	 * @return array
	 */
	public function getInfo(){
		return $this->info;
	}

	/**
	 * ��������
	 *
	 */
	public function __destruct(){
		curl_close($this->ch);
	}

	/**
	 * ִ������
	 *
	 * @param string $method :HTTP����ʽ
	 * @param string $url :�����URL
	 * @param array $params ������Ĳ���
	 * @param array $uploadFile :�ϴ����ļ�(ֻ��POSTʱ����Ч)
	 * @param array $referer :����ҳ��
	 * @return ���󷵻�:false ��ȷ����:�������
	 */
	private function _request($method, $url, $params=array(), $uploadFile=array(), $referer=''){

		//�������GET��ʽ������Ҫ���ӵ�URL����
		if($method == 'GET'){
			$url = $this->_parseUrl($url,$params);
		}

		curl_setopt($this->ch, CURLOPT_URL, $url);	//���������URL

		//�����POST
		if($method == 'POST'){
			curl_setopt($this->ch, CURLOPT_POST, true) ;	//����һ�������POST��������Ϊ��application/x-www-form-urlencoded
			$postData = $this->_parsmEncode($params,false);	//����POST�ֶ�ֵ

			//������ϴ��ļ�
			if($uploadFile){
				foreach($uploadFile as $key=>$file){
					if(is_array($file)){
						$n = 0;
						foreach($file as $f){
							$postData[$key.'['.$n++.']'] = '@'.realpath($f);	//�ļ������Ǿ���·��
						}
					}else{
						$postData[$key] = '@'.realpath($file);
					}
				}
			}
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
		}

		//����������ҳ,�����Զ�����
		if($referer){
			curl_setopt($this->ch, CURLOPT_REFERER, $referer);
		}else{
			curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
		}

		$this->info['before'] = curl_getinfo($this->ch);				//�õ��������õ���Ϣ
		$result = curl_exec($this->ch);									//��ʼִ������

		$headerSize = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);	//�õ�����ͷ
		$this->info['header'] = substr($result, 0, $headerSize);

		$result = substr($result, $headerSize);							//ȥ������ͷ
		$this->info['after'] = curl_getinfo($this->ch);					//�õ����а������������ص���Ϣ

		//�������ɹ�
		if($this->errno() == 0){ //&& $this->info['after']['http_code'] == 200
			return $result;
		}else{
			return false;
		}

	}

	/**
	 * ���ؽ������URL��GET��ʽʱ���õ�
	 *
	 * @param string $url :URL
	 * @param array $params :����URL��Ĳ���
	 * @return string
	 */
	private function _parseUrl($url,$params){
		$fieldStr = $this->_parsmEncode($params);
		if($fieldStr){
			$url .= strstr($url,'?')===false ? '?' : '&';
			$url .= $fieldStr;
		}
		return $url;
	}

	/**
	 * �Բ�������ENCODE����
	 *
	 * @param array $params :����
	 * @param bool $isRetStr : true�����ַ������� false:�����鷵��
	 * @return string || array
	 */
	private function _parsmEncode($params,$isRetStr=true){
		$fieldStr = '';
		$spr = '';
		$result = array();
		foreach($params as $key=>$value){
			$value = urlencode($value);
			$fieldStr .= $spr.$key .'='. $value;
			$spr = '&';
			$result[$key] = $value;
		}
		return $isRetStr ? $fieldStr : $result;
	}
}

