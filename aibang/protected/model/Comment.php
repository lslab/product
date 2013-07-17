<?php

Doo::loadCore('db/DooModel');

class Comment extends DooModel{

public $id;
public $url_id;
public $name;
public $content;
public $ok;

	public $_table = 'comment';
	public $_primarykey = 'id';
	public $_fields = array('id','url_id','name','content','ok');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}



}
?>