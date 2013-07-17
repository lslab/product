<?php
class myRedis {

	protected static $_redis;
	
	public static function redis()
	{
		if (!self::$_redis) {
			self::$_redis=new Redis();
			self::$_redis->connect('127.0.0.1');
		}
		return self::$_redis;
	}//build
	
	public static function set($key,$val)
	{
		return self::redis()->rPush($key,$val);
	}//set
	
	public static function get($key)
	{
		return self::redis()->rPop($key);
	}//get
	
	public static function delete($key)
	{
		return self::redis()->delete($key);
	}//delete
}