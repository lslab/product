<?php

Doo::loadCore('db/DooModel');

class Thread extends DooModel{

	public $tid;
	public $subject;

	public $_table = 'pre_forum_thread';
	public $_primarykey = 'tid';
	public $_fields = array('tid','subject');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


}
?>