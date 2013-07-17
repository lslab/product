<?php

$route['*']['/'] = array('MainController', 'index');
$route['*']['/list'] = array('MainController', 'getlist');
$route['*']['/list/:pid'] = array('MainController', 'getlist');
$route['*']['/info'] = array('MainController', 'getinfo');
$route['*']['/post'] = array('PostController', 'post');


?>