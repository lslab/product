<?php
/**
 * PHPFW公共文件
 */
if(version_compare(PHP_VERSION,'5.0.0','<') ) {
	die('Require PHP > 5.0 !');
}

//框架版本
define('PHPFW_VERSION','1.0');

//记录开始运行时间
$_beginTime = microtime(true);

//检测该定制的常量
if (!defined('APP_PATH'))	{die('You must defined APP_PATH first!');}
if (defined('PHPFW_PATH'))	{die('Global variant "PHPFW_PATH" is the private variant of system and you can\'t defined it!');}
if (!defined('APP_NAME'))	{define('APP_NAME',md5($_SERVER['SCRIPT_NAME']));}
// PHPFW系统目录定义
define('PHPFW_PATH', dirname(__FILE__));

require(PHPFW_PATH.'/core/Base.class.php');
require(PHPFW_PATH.'/core/App.class.php');

//将某些变量存入App的静态属性中，以方便全局调用
App::$globals['beginTime']=$_beginTime;

App::$globals['classFiles']=array(
	'Controller'	=>	PHPFW_PATH.'/core/Controller.class.php',
	'Model'			=>	PHPFW_PATH.'/core/Model.class.php',
	'Validator'		=>	PHPFW_PATH.'/core/model/Validator.class.php',
	'Router'		=>	PHPFW_PATH.'/core/Router.class.php',
	'View'			=>	PHPFW_PATH.'/core/View.class.php',
	'Template'		=>	PHPFW_PATH.'/core/view/Template.class.php',
	'DBase'			=>	PHPFW_PATH.'/core/model/DBase.class.php',
	'DB'			=>	PHPFW_PATH.'/core/model/DB.class.php',
	'Cache'			=>	PHPFW_PATH.'/libs/cache/Cache.class.php',
	'CacheOnFile'	=>	PHPFW_PATH.'/libs/cache/CacheOnFile.class.php',
	
	'Form'			=>	PHPFW_PATH.'/helper/Form.class.php',

	'HttpClient'	=>	PHPFW_PATH.'/libs/HttpClient.class.php',
	'Curl'	=>	PHPFW_PATH.'/libs/Curl.class.php',
);

function __autoload($className)
{
	if (isset(App::$globals['classFiles'][$className])) {
		irequire(App::$globals['classFiles'][$className]);
	} elseif (is_file($file=APP_PATH.'/model/'.$className.'.class.php')) {
		irequire($file);
	} elseif (is_file($file=APP_PATH.'/controller/'.$className.'.class.php')) {
		irequire($file);
	}
}

//$_GET $_POST $_COOKIE 取消自动转义
if (get_magic_quotes_gpc()) {
	$_GET = array_stripslashes($_GET);
	$_POST = array_stripslashes($_POST);
	$_COOKIE = array_stripslashes($_COOKIE);
}

############## buildin functions ###########

//$_GET $_POST $_COOKIE 取消自动转义
function array_stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = array_stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function irequire($file)
{
	$file=realpath($file);
	if (!in_array($file,(array)App::$includeFiles)) {
		App::$includeFiles[]=$file;
		return require($file);
	}
}

?>