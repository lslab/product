<?php

Doo::loadCore('db/DooModel');

class Url extends DooModel{

public $id;
public $keyword;
public $province;
public $city;
public $country;
public $url;
public $title;
public $titleimg;
public $ok;
public $ok2;
public $comment_ok;

	public $_table = 'url';
	public $_primarykey = 'id';
	public $_fields = array('id','keyword','province','city','country','url','title','titleimg','ok','ok2','comment_ok');


	public function  __construct($data=null) {
		parent::__construct( $data );
		parent::setupModel(__CLASS__);
	}


	/**
	 * 保存数据
	 *
	 * @param unknown_type $data
	 * @return 0失败，1成功，2已存在
	 */
	public function save($data)
	{
		$this->url=$data['url'];
		if (!$this->find()) {
			if ($this->insertAttributes($data)) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 2;
		}
	}//save
	
	public function echoSaveResult($id,$title=null)
	{
		switch ($id) {
			case 0:
				$r="<font color='red'>[保存失败]</font>";
				break;
			case 1:
				$r="<font color='green'>[保存成功]</font>";
				break;
			case 2:
				$r="<font color='blue'>[已经存在]</font>";
				break;
		}
		echo "$r\t{$title}<br>\n";
	}//saveResult

}
?>