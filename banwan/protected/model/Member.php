<?php

Doo::loadCore('db/DooModel');

class Member extends DooModel{

	public $id;
	public $name;
	public $img;
	public $imgok;
	public $sex;
	public $age;
	public $province;
	public $city;
	public $info;
	public $tel;
	public $date;
	public $ok;
	public $publish;

	public $_table = 'member';
	public $_primarykey = 'id';
	public $_fields = array('id','name','img','imgok','sex','age','province','city','info','tel','date','ok','publish');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>