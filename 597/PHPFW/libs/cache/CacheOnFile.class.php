<?php

class CacheOnFile extends Base {

	public $cacheDir = '/cache';
	public $lifeTime = 0;	//为0时永远有效
	public $dirLevels = 0;	//缓存保存时的目录层数
	
	private $md5id;	//md5后的缓存id

	const MAX_CACHE_DIR_LEVELS = 16;	//最大缓存目录层次
	
	public function __construct($config=array())
	{
		foreach ($config as $key=>$val) {
			$this->$key=$val;
		}
		$this->cacheDir=rtrim($this->cacheDir,'/');
		
		if ($this->dirLevels>self::MAX_CACHE_DIR_LEVELS) {
			$this->dirLevels=self::MAX_CACHE_DIR_LEVELS;
		}
	}
	
	/**
	 * 读取缓存（无缓存时间，只要缓存存在就读取）
	 *
	 * @param string $id
	 * @return mixed|boolean
	 */
	public function get($id)
	{
		$this->md5id=md5($id);
		$file=$this->getCacheFile($this->md5id);
		if ( is_file($file) ) {
			return unserialize(file_get_contents($file));
		} else {
			return null;
		}
	}
	
	/**
	 * 删除缓存
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	public function delete($id)
	{
		echo $file=$this->getCacheFile(md5($id));
		if (is_file($file)) {
			return @unlink($file);
		} else {
			return false;
		}
	}//delete
	
	/**
	 * 读取有效时间内的缓存，有有效期的限制
	 *
	 * @param string $id
	 * @return mixed|boolean
	 */
	public function getValid($id)
	{
		$this->md5id=md5($id);
		$file=$this->getCacheFile($this->md5id);
		if ( is_file($file) && ( !$this->lifeTime || filemtime($file)>time() ) ) {
			return unserialize(file_get_contents($file));
		} else {
			return false;
		}
	}
	
	/**
	 * 保存缓存
	 *
	 * @param mixed $data
	 * @param string $id
	 */
	public function save($id,$data)
	{
		$lifeTime=time() + (strlen($lifeTime) ? $lifeTime : $this->lifeTime);
		$md5id=strlen($id) ? md5($id) : $this->md5id;
		$dir=$this->cacheDir.'/'.$this->getDirLevel($md5id);
		if (!file_exists($dir)) {
			mkdir($dir,0777,true);
		}
		$cacheFile=$dir.'/'.$md5id.'.cache.php';
		//$cacheFile=$this->getCacheFile($md5id);
		file_put_contents($cacheFile,serialize($data),LOCK_EX);
		$this->lifeTime && touch($cacheFile,$lifeTime);
	}
	
	/**
	 * 获取缓存文件名
	 *
	 */
	private function getCacheFile($md5id)
	{
		$dirLevel=$this->getDirLevel($md5id);
		if (strlen($dirLevel)) {
			$dirLevel.='/';
		}
		return $this->cacheDir.'/'.$dirLevel.$md5id.'.cache.php';
	}
	
	/**
	 * 获取缓存目录层次
	 *
	 */
	private function getDirLevel($md5id)
	{
		$levels=array();
		$levelLen=2;
		for ($i=0; $i<$this->dirLevels; $i++) {
			$levels[]=substr($md5id,$i*$levelLen,$levelLen);
		}
		return !count($levels) ? '' : implode('/',$levels);
	}
}//class


?>