<?php
	
class prodModel extends Model {
	
	public $tableName='prod';
	public $primaryKey='id';//	
	protected $fields=array('id','name','content','fid','ok');

}

?>