<?php

Doo::loadCore('db/DooModel');

class Afcity extends DooModel{

public $id;
public $name;
public $pid;

	public $_table = 'afcity';
	public $_primarykey = 'id';
	public $_fields = array('id','name','pid');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>