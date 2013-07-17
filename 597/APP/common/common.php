<?php

function gbk($s)
{
	return mb_convert_encoding($s,'GBK','UTF-8');
}//gbk

function pp($v,$d=null)
{
	echo '<pre>';
//	print_r($v);
	ob_start();
	var_dump($v);
	$s=ob_get_contents();
	ob_clean();
	$s=preg_replace('/=>\n/','=>',$s);
	$s=preg_replace('/=>\n/','=>',$s);
	echo $s;
	echo "</pre>\n";
	$d ? die : null;
}
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
		return null;
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
 * 调用迅雷下载
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