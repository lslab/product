<?php

class Cache extends Base {

	private static $objCache;	//缓存类实例
	
	private function init()
	{
		if (!self::$objCache) {
			$options=array(
				'cacheDir'	=>	App::config('cache_dir'),
				'lifeTime'	=>	App::config('cache_life_time'),
				'dirLevels'	=>	App::config('cache_dir_levels'),
			);
			self::$objCache=new CacheOnFile($options);
		}
	}
	
	/**
	 * 读取缓存（无缓存时间，只要缓存存在就读取）
	 *
	 * @param string $id
	 * @return mixed|boolean
	 */
	public static function get($id,$default=null)
	{
		self::init();
		$r = self::$objCache->get($id);
		return $r!==null ? $r : $default;
	}
	
	/**
	 * 读取有效时间内的缓存，有有效期的限制
	 *
	 * @param string $id
	 * @return mixed|boolean
	 */
	public function getValid($id)
	{
		self::init();
		return self::$objCache->getValid($id);
	}//readValid
	
	/**
	 * 保存缓存
	 *
	 * @param mixed $data
	 * @param string $id
	 */
	public static function save($id,$data)
	{
		self::init();
		return self::$objCache->save($id,$data);
	}
	
	/**
	 * 删除缓存
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	public static function delete($id)
	{
		self::init();
		return self::$objCache->delete($id);
	}//delete
	
}//class


?>