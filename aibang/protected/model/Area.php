<?php

Doo::loadCore('db/DooModel');

class Area extends DooModel{

public $id;
public $province;
public $city;
public $country;
public $ok;

	public $_table = 'area';
	public $_primarykey = 'id';
	public $_fields = array('id','province','city','country','ok');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>