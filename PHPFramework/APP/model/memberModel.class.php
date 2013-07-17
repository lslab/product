<?php
class memberModel extends Model {
	
	public $tableName='members';
	public $primaryKey='uid';
//	public $tablePrefix='cdb_';

//	protected $fields=array('uid','username','password','email','created','modified');
	
	protected $autoFields=array(
		array('created','time',1,'f'),
		array('modified','time',2,'f'),
		array('password','md5',3,'f'),
	);
}

?>