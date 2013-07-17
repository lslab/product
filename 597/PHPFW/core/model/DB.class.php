<?php
/**
 * 数据库类
 * 
 * 在此类中使用query()时，会自动将SQL语句中的"__PREFIX__"替换为app::config('db_table_prefix')
 *
 */

class DB extends DBase {

	//SQL语句中代表数据表前缀的字符串
	const TABLE_PREFIX = '__TABLE_PREFIX__';
	
	public function __construct()
	{
		return parent::__construct(
			App::config('db_host'),
			App::config('db_user'),
			App::config('db_password'),
			App::config('db_database'),
			App::config('db_charset'),
			App::config('db_pconnect')
		);
	}//__construct(
	
	public function query($sql)
	{
		$sql=str_replace(self::TABLE_PREFIX, App::config('db_table_prefix'), $sql);
		return parent::query($sql);
	}//query
	
}