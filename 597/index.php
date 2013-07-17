<?php
header('Content-Type: text/html; charset=gbk');
session_start();

define('APP_PATH',dirname(__FILE__).'/APP');

require(dirname(__FILE__).'/PHPFW/PHPFW.php');
$app=new App();
$app->run();