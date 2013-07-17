<?php

Doo::loadCore('db/DooModel');

class Member extends DooModel{

	public $id;
	public $name;
	public $img;
	public $province;
	public $city;
	public $county;
	public $info;
	public $email;
	public $qq;
	public $realQQ;
	public $url;
	public $date;
	public $ok;
	public $publish;

	public $_table = 'member';
	public $_primarykey = 'id';
	public $_fields = array('id','name','img','province','city','county','info','email','qq','realQQ','url','ok','publish','date');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>