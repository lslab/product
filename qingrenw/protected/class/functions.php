<?php


function imgCode($imgcon)
{

	$numCodes=array
 (
  0 => '11100001111100000011100111100100111111000011111100100111100111000000111110000111',
  1 => '11111111111101111110100111111000000000000000000000111111111011111111101111111111',
  2 => '11011111101001111100001111100001111100100111100110001100111010000111101100111110',
  3 => '10111111010011111100011111111001110111100111011110001000110010001000011101110011',
  4 => '11111001111111000111111001011111001101111001110111000000000000000000001111110111',
  5 => '00000110110000011001011101110001101111100110111110011001110001110000011111100011',
  6 => '11000000111000000001001110110001110111100111011110001100110010011000011111110011',
  7 => '01111111000111111000011111001101111001110111001111011001111100001111110001111111',
  8 => '11011100111000100001001000110001110111100111011110001000110010001000011101110011',
  9 => '11001111111000011001001100110001111011100111101110001101110010000000011100000011',
    );

	$left	= 12;
	$top	= 5;
	$space	= 1;
	$width	= 8;
	$height	= 10;

	//	$img = imagecreatefrompng($imgfile);
	$img = imagecreatefromstring($imgcon);

	$color_average=0;	//颜色平均数，从getNumStr.php中可以获取此值

	//获取4个数字的每个像素的颜色
	for ($i=0; $i<5; $i++) {
		for ($k=0; $k<$width; $k++) {
			for ($j=0; $j<$height; $j++) {
				$x = $left + $k + ($width * $i) + ($space * $i);
				$y = $top + $j;

				$color=imagecolorat($img,$x,$y);
				//echo "$x,$y	$color\n";
				$colorIndex[$i] .= $color==$color_average ? 1 : 0;
			}//for j
		}//for k
	}//for k


	//比较、识别
	$result='';	//结果
	for ($i=0; $i<5; $i++) {
		$imgColors=$colorIndex[$i];	//图片上数字特征码
		$_result=array();	//单个数字的识别结果
		for ($k=0; $k<10; $k++) {
			$numColors=$numCodes[$k];	//已经获取的数字标准特征码
			for ($j=0; $j<80; $j++) {	//每个数字特征码的长度为60字节
				if ($imgColors[$j]==$numColors[$j]) {
					$_result[$k]+=1;
				}
			}
		}
		$num=array_search(max($_result),$_result);
		$result.=$num;
	}

	return $result;

}

function tableFields($table)
{
	mysql_connect('localhost','root','');
	mysql_select_db('autohome');
	mysql_query('set names gbk');
	$query = mysql_query("DESCRIBE `$table`");
	$r=null;
	while ($row = mysql_fetch_assoc($query)) {
		$r[]=$row['Field'];
	}
	return $r;
}//fiels


function validFileName($fname,$flag='')
{
	$s='\/:*?"<>|';
	for ($i=0; $i<strlen($s); $i++) {
		$fname=str_replace($s[$i],$flag,$fname);
	}
	return $fname;
}//validFileName

function getip ()
{
	global $_SERVER;
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} else if (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} else if (getenv('REMOTE_ADDR')) {
		$ip = getenv('REMOTE_ADDR');
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function pp($v,$d=null)
{
	if (is_string($v) && !strlen($v)) {
		$v='string("")';
	} elseif (is_null($v)) {
		$v='null';
	} elseif (is_bool($v)) {
		$v=$v?'True':'False';
	} else {
		$v=print_r($v,true);
	}
	echo "<pre>{$v}</pre>\n";
	$d ? die : null;
}

function getTagData($str, $start, $end){
	if (!empty($start)) {
		$str = explode($start, $str, 2);
		$str = $str[1];
	}
	if (!empty($end)) {
		$str = explode($end, $str, 2);
		$str = $str[0];
	}
	return $str;
}

function urljoin($baseurl,$url)
{
	if (!preg_match('/\/$/',$baseurl))
	$baseurl = substr($baseurl,0,strlen($baseurl)-strlen(basename($baseurl))-1);
	else
	$baseurl = substr($baseurl,0,strlen($baseurl)-1);
	$arrurl = explode('/',str_replace('http://','',$baseurl));
	$arrurl[0] = 'http://'.$arrurl[0];

	$v=trim($url);
	#http://
	if (preg_match("/^(http|https|ftp)?:\/\//i",$v))
	$result = $v;
	#/demo.gif
	elseif (preg_match("/^\//",$v))
	$result = $arrurl[0].$v;
	#demo.gif
	elseif (preg_match("/^[\w-]/",$v))
	$result = $baseurl.'/'.$v;
	#./demo.gif
	elseif (preg_match("/^\.\//",$v))
	$result = $baseurl.'/'.substr($v,2,strlen($v)-1);
	#../
	elseif (preg_match("/^[\.\.\/]+/",$v))
	{
		$ar = explode('../',$v);
		$upperPathCount = count($ar)-1;
		$ar2 = $arrurl;
		for ($i=0; $i<$upperPathCount; $i++)
		array_pop($ar2);
		$result = join('/',$ar2).'/'.$ar[count($ar)-1];
	}
	return $result;
}

function regMatch($str, $regStr, $striphtml=true)
{
	if (preg_match($regStr, $str, $result)) {
		if (isset($result[1])) {
			$r= trim($result[1]);
		} else {
			$r= trim($result[0]);
		}
		$rr= $striphtml ? strip_tags($r) : $r;
		return trim($rr);
	} else {
		return '';
	}
}


function clearHtml($html)
{
	$html=preg_replace('/<script.*?<\/script>/is','',$html);
	$html=preg_replace('/<iframe.*?<\/iframe>/is','',$html);
	$html=strip_tags($html,'<br/><br /><br><p>');
	$html=trim($html);
	return $html;
}//clear

/**
 * ����Ѹ������
 *
 * @param unknown_type $url
 * @param unknown_type $fileName
 * @param unknown_type $savePath
 * @param unknown_type $comment
 * @param unknown_type $referer
 * @param unknown_type $startMode
 * @param unknown_type $onlyFromOrigin
 * @param unknown_type $originThreadCount
 */
function thunder($url,$fileName,$savePath,$comment='',$referer='',$startMode=1,$onlyFromOrigin=1,$originThreadCount=3)
{
	$ThunderAgent = new COM("ThunderAgent.Agent.1");
	$ThunderAgent->AddTask($url,$fileName,$savePath,$comment,$referer,$startMode,$onlyFromOrigin,$originThreadCount);
	$ThunderAgent->CommitTasks2(1);
	unset($ThunderAgent);
}


//======================================================//

/**
 * Returns an array of all the given parameters.
 *
 * Example:
 *
 * `a('a', 'b')`
 *
 * Would return:
 *
 * `array('a', 'b')`
 *
 * @return array Array of given parameters
 * @link http://book.cakephp.org/view/1122/a
 * @deprecated Will be removed in 2.0
 */
function a() {
	$args = func_get_args();
	return $args;
}

/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 *
 * `aa('a','b')`
 *
 * Would return:
 *
 * `array('a'=>'b')`
 *
 * @return array Associative array
 * @link http://book.cakephp.org/view/1123/aa
 * @deprecated Will be removed in 2.0
 */
function aa() {
	$args = func_get_args();
	$argc = count($args);
	for ($i = 0; $i < $argc; $i++) {
		if ($i + 1 < $argc) {
			$a[$args[$i]] = $args[$i + 1];
		} else {
			$a[$args[$i]] = null;
		}
		$i++;
	}
	return $a;
}


/**
 * Convenience method for strtolower().
 *
 * @param string $str String to lowercase
 * @return string Lowercased string
 * @link http://book.cakephp.org/view/1134/low
 * @deprecated Will be removed in 2.0
 */
function low($str) {
	return strtolower($str);
}

/**
 * Convenience method for strtoupper().
 *
 * @param string $str String to uppercase
 * @return string Uppercased string
 * @link http://book.cakephp.org/view/1139/up
 * @deprecated Will be removed in 2.0
 */
function up($str) {
	return strtoupper($str);
}

/**
 * Convenience method for str_replace().
 *
 * @param string $search String to be replaced
 * @param string $replace String to insert
 * @param string $subject String to search
 * @return string Replaced string
 * @link http://book.cakephp.org/view/1137/r
 * @deprecated Will be removed in 2.0
 */
function r($search, $replace, $subject) {
	return str_replace($search, $replace, $subject);
}

/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 * @link http://book.cakephp.org/view/1124/am
 */
function am() {
	$r = array();
	$args = func_get_args();
	foreach ($args as $a) {
		if (!is_array($a)) {
			$a = array($a);
		}
		$r = array_merge($r, $a);
	}
	return $r;
}

/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/view/1130/env
 */
function env($key) {
	if ($key == 'HTTPS') {
		if (isset($_SERVER['HTTPS'])) {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		}
		return (strpos(env('SCRIPT_URI'), 'https://') === 0);
	}

	if ($key == 'SCRIPT_NAME') {
		if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
			$key = 'SCRIPT_URL';
		}
	}

	$val = null;
	if (isset($_SERVER[$key])) {
		$val = $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		$val = $_ENV[$key];
	} elseif (getenv($key) !== false) {
		$val = getenv($key);
	}

	if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
		$addr = env('HTTP_PC_REMOTE_ADDR');
		if ($addr !== null) {
			$val = $addr;
		}
	}

	if ($val !== null) {
		return $val;
	}

	switch ($key) {
		case 'SCRIPT_FILENAME':
			if (defined('SERVER_IIS') && SERVER_IIS === true) {
				return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
			}
			break;
		case 'DOCUMENT_ROOT':
			$name = env('SCRIPT_NAME');
			$filename = env('SCRIPT_FILENAME');
			$offset = 0;
			if (!strpos($name, '.php')) {
				$offset = 4;
			}
			return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
			break;
		case 'PHP_SELF':
			return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
		case 'CGI_MODE':
			return (PHP_SAPI === 'cgi');
			break;
		case 'HTTP_BASE':
			$host = env('HTTP_HOST');
			if (substr_count($host, '.') !== 1) {
				return preg_replace('/^([^.])*/i', null, env('HTTP_HOST'));
			}
			return '.' . $host;
			break;
	}
	return null;
}


/**
 * Convenience method for htmlspecialchars.
 *
 * @param string $text Text to wrap through htmlspecialchars
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/view/1132/h
 */
function h($text, $charset = null) {
	if (is_array($text)) {
		return array_map('h', $text);
	}

	static $defaultCharset = false;
	if ($defaultCharset === false) {
		$defaultCharset = App::config('page_charset');
		if ($defaultCharset === null) {
			$defaultCharset = 'UTF-8';
		}
	}
	if ($charset) {
		return htmlspecialchars($text, ENT_QUOTES, $charset);
	} else {
		return htmlspecialchars($text, ENT_QUOTES, $defaultCharset);
	}
}

/**
 * ��ӵ��ļ�
 *
 * @param string $file
 * @param string $content
 * @return boolean
 */
function append($file,$content) {
	if ($fp=@fopen($file,'a+')) {
		fwrite($fp,$content);
		fclose($fp);
		return true;
	} else {
		return false;
	}
}

/**
 * �����ļ�
 *
 * @param string $file
 * @param string $content
 * @return boolean
 */
function save($file,$content) {
	return file_put_contents($file,$content);
}

/**
 * ��ȡ�ļ�����
 *
 * @param string $file
 * @return string
 */
function read($file) {
	return file_get_contents($file);
}

function getFileExt($filename)
{
	$arr=explode('.',$filename);
	return '.'.$arr[count($arr)-1];
}//getFileExt
