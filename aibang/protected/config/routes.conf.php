<?php

$route['*']['/'] = array('MainController', 'index');
$route['*']['/info'] = array('InfoController', 'index');
$route['*']['/img'] = array('InfoController', 'img');
$route['*']['/pages'] = array('MainController', 'pages');
$route['*']['/comment'] = array('CommentController', 'index');
$route['*']['/post'] = array('PostController', 'index');

$route['*']['/page/:pindex'] = array('BlogController', 'page');


?>