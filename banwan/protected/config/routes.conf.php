<?php

$route['*']['/'] = array('MainController', 'index');
$route['*']['/get'] = array('MainController', 'get');
$route['*']['/info'] = array('MainController', 'info');
$route['*']['/post'] = array('PostController', 'index');


?>