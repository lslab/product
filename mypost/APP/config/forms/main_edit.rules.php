<?php
return array(

	'username'	=>	array(
//		array('username',null,null,'非法的用户名格式'),
		array('maxlength',10,'不能长于10字符'),
		array('minlength',3,'不能短于3字符'),
		//array('dbNotExist','members','username','用户名已经存在'),
	),
	'password'	=>	array(
		array('minlength',6,'密码不得适于6个字符'),
	),
	'email'		=>	array(
		array('email','非法的邮件格式'),
		array('optional'),
	),

);