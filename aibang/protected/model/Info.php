<?php

Doo::loadCore('db/DooModel');

class Info extends DooModel{

public $id;
public $ord;
public $address;
public $tel;
public $price;
public $openhours;
public $tags;
public $intro;
public $id2;
public $ok;

	public $_table = 'info';
	public $_primarykey = 'id';
	public $_fields = array('id','address','tel','price','openhours','tags','intro','id2','ok','ord');
	


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}



}
?>