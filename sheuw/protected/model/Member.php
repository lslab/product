<?php

Doo::loadCore('db/DooModel');

class Member extends DooModel{

public $autoid;
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
public $email;
public $qq;
public $url;
public $y;
public $m;
public $d;
public $ok;

	public $_table = 'member';
	public $_primarykey = 'id';
	public $_fields = array('autoid','id','name','img','imgok','sex','age','province','city','info','tel','email','qq','url','y','m','d','ok');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>