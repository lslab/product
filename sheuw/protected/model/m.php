<?php

Doo::loadCore('db/DooModel');

class m extends DooModel{

	public $id;
	public $name;
	public $img;
	public $smallImg;
	public $sex;
	public $age;
	public $province;
	public $city;
	public $county;
	public $info;
	public $tel;
	public $email;
	public $qq;
	public $realQQ;
	public $url;
	public $ok;

	public $_table = 'm';
	public $_primarykey = 'id';
	public $_fields = array('id','name','img','smallImg','sex','age','province','city','county','info','tel','email','qq','realQQ','url','ok');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>