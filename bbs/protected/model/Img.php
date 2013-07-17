<?php

Doo::loadCore('db/DooModel');

class Img extends DooModel{

public $id;
public $attachment;
public $ok;

	public $_table = 'Img';
	public $_primarykey = 'tid';
	public $_fields = array('tid','attachment','ok');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>