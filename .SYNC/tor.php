<?php

for ($i=0; $i<1; $i++) {


	$con= tor_wrapper('http://www.cmyip.com/');
	preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/',$con,$ar);
	$ip=$ar[0];
	
	$fp=fopen(dirname(__FILE__).'/ip.txt','a');
	echo $s=$ip."\t".date("Y-m-d H:i:s")."\t";
	fwrite($fp,$s);
	fwrite($fp,"\n");
	fclose($fp);

	tor_new_identity();
	echo "更新身份\n";
	sleep(2);

}

//更改身份（控制TOR更改IP）
function tor_new_identity($tor_ip='127.0.0.1', $control_port='9051', $auth_code=''){
//	return true;
    $fp = fsockopen($tor_ip, $control_port, $errno, $errstr, 30);
    if (!$fp) return false; //can't connect to the control port
 
    fputs($fp, "AUTHENTICATE $auth_code\r\n");
    $response = fread($fp, 1024);
    list($code, $text) = explode(' ', $response, 2);
    if ($code != '250') return false; //authentication failed
 
    //send the request to for new identity
    fputs($fp, "signal NEWNYM\r\n");
    $response = fread($fp, 1024);
    list($code, $text) = explode(' ', $response, 2);
    if ($code != '250') return false; //signal failed
 
    fclose($fp);
    return true;
}
 
function tor_wrapper($url) {
    $ua = array('Mozilla','Opera','Microsoft Internet Explorer','ia_archiver');
    $op = array('Windows','Windows XP','Linux','Windows NT','Windows 2000','OSX');
    $agent  = $ua[rand(0,3)].'/'.rand(1,8).'.'.rand(0,9).' ('.$op[rand(0,5)].' '.rand(1,7).'.'.rand(0,9).'; en-US;)';
    
    # Tor address & port
    $tor = '127.0.0.1:9050';
    //$tor = '127.0.0.1:8118';
    # set a timeout.
    $timeout = '300';
    $ack = curl_init();
    curl_setopt ($ack, CURLOPT_PROXY, $tor);
    curl_setopt ($ack, CURLOPT_URL, $url);
    curl_setopt ($ack, CURLOPT_HEADER, 1);
    curl_setopt ($ack, CURLOPT_USERAGENT, $agent);
    curl_setopt ($ack, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ack, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ack, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($ack, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    $syn = curl_exec($ack);
    # $info = curl_getinfo($ack);
    curl_close($ack);
    # $info['http_code'];
    return $syn;
}


