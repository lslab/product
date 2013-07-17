<?php
/*
CREATE TABLE `config` (
  `cfgKey` varchar(50) NOT NULL,
  `cfgValue` varchar(255) NOT NULL,
  `grp` varchar(20) NOT NULL,
  PRIMARY KEY (`cfgKey`),
  KEY `group` (`grp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
	
class configModel extends Model {
	
	public $tableName='config';
	public $primaryKey='cfgKey';
//	public $tablePrefix='cdb_';
	public $group='';	

	public function read($k,$default=null)
	{
		$result = $this->findField('cfgValue',array('cfgKey'=>$k,'grp'=>$this->group));
		if (!strlen($result) && $default!==null) {
			$result=$default;
		}
		return $result;
	}//read
	
	public function write($key,$value)
	{
		if ($key=='') return;
		$row=$this->find(null,array('cfgKey'=>$key,'grp'=>$this->group));
		if ($row) {
			return $this->update(array('cfgValue'=>$value),array('cfgKey'=>$key));
		} else {
			return $this->create(array('cfgKey'=>$key,'cfgValue'=>$value,'grp'=>$this->group));
		}
	}//save
	
	public function readAll()
	{
		$rs=$this->findAll(null,array('grp'=>$this->group));
		$r=array();
		foreach ((array)$rs as $row) {
			$r[$row['cfgKey']]=$row['cfgValue'];
		}
		return $r;
	}//readAll
	
	public function remove($key)
	{
		parent::delete(array('cfgKey'=>$key,'grp'=>$this->group));
	}//delete
}

?>