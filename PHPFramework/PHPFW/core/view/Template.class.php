<?php

class Template extends Base
{
	public $templateDir = 'templates';
	public $leftTag = '{';
	public $rightTag = '}';
	public $compileDir = 'cache';
	public $compiledFileExt = '.TemplateCompiled.php';
	public $templateFileExt = '.html';	//当display() cache() 不使用参数时使用
	public $caching = false;
	public $cacheDir = 'cache';
	public $cacheDirLevels = 0;			//缓存目录层次
	public $cacheFileExt = '.TemplateCache.php';
	public $cacheLifeTime = 3600; 		// 单位 秒
	public $cacheID;
	public $forceCompile = false;
	public $lang=array();
	
	private $cacheFile;				//缓存文件，在_saveCache()中使用
	private $realCacheID;			//通过计算得出的缓存ID
	
	const MAX_CACHE_DIR_LEVELS=16;	//最大缓存目录层次数量
	
	public function __construct($arrConfig = array())
	{
		foreach ($arrConfig as $key=>$val) {
			$this->$key = $val;
		}
		
		if ($this->cacheDirLevels>self::MAX_CACHE_DIR_LEVELS) {
			$this->cacheDirLevels=self::MAX_CACHE_DIR_LEVELS;
		}
	}
	
	/**
	 * 判断缓存文件是否有效
	 *
	 * @param string $file
	 * @param string $cacheID
	 * @return boolean
	 */
	public function cached($file='',$cacheID='')
	{
		$file=$this->getTemplateFile($file);
		$this->cacheID=$cacheID;
		$cachefile=$this->getCacheFileName($file,$cacheID);
		if ($this->caching && is_file($cachefile) && (filemtime($cachefile)+$this->cacheLifeTime)>time()) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 返回模板文件完整路径
	 *
	 * @param string $file
	 * @return string
	 */
	private function getTemplateFile($file='')
	{
		if (!strlen($file)) {
			$file=App::$controller.'_'.App::$action.$this->templateFileExt;
		}
		return $file;
	}
	
	/**
	 * 获取缓存文件完整路径
	 *
	 * @param string $file
	 * @param string $cacheID
	 * @return string
	 */
	private function getCacheFileName($file,$cacheID)
	{
		if (!strlen($this->realCacheID)) {
			$this->realCacheID=$cacheID!=''?$cacheID:$_SERVER['SCRIPT_NAME'].$_SERVER['QUERY_STRING'];
			$this->realCacheID.=$this->templateDir.$file.APP_NAME;
		}
		$md5id=md5($this->realCacheID);
		$this->cacheDirLevel=$this->getCacheDirLevel($md5id);
		return $this->cacheDir.$this->cacheDirLevel.'/'.$md5id.$this->cacheFileExt;
	}
	
	/**
	 * 获取缓存目录层次
	 *
	 */
	private function getCacheDirLevel($md5id)
	{
		$levels=array();
		$levelLen=2;
		for ($i=0; $i<$this->cacheDirLevels; $i++) {
			$levels[]='TepmlateCache_'.substr($md5id,$i*$levelLen,$levelLen);
		}
		return !count($levels) ? '' : '/'.implode('/',$levels);
	}
	
	/**
	 * 在$this->compile()中替换$foo.var为数组格式$foo['var']
	 *
	 */
	private function compile_replace($str)
	{
		$str=preg_replace('/(\$[a-z_]\w*)\.([\w]+)/',"\\1['\\2']",$str);
		return $this->leftTag.$str.$this->rightTag;
	}
	
	/**
	 * 编译模板文件
	 *
	 * @param string $file
	 * @return string
	 */
	private function compile($file='')
	{
		$file=$this->getTemplateFile($file);
		$fullTplPath=$this->templateDir.'/'.$file;
		$compiledFile=$this->compileDir.'/'.md5($fullTplPath).$this->compiledFileExt;
		if ($this->forceCompile || !is_file($compiledFile) || filemtime($compiledFile)<=filemtime($fullTplPath)) {
			$content=file_get_contents($fullTplPath);
			$leftTag=preg_quote($this->leftTag);
			$rightTag=preg_quote($this->rightTag);
			$search=array(
				'/'.$leftTag.'include ([\w\.\/-]+)'.$rightTag.'/i',			//导入子模板
				'/'.$leftTag.'(\$[a-z_]\w*)\.(\w+)'.$rightTag.'/i',			//将模板标签{$foo.var}修改为数组格式{$foo['var']}
				'/'.$leftTag.'(.+?\$[a-z_]\w*\.\w+.*?)'.$rightTag.'/ie',	//将模板标签中的$foo.var修改为数组格式$foo['var']
				'/'.$leftTag.'(else if|elseif) (.*?)'.$rightTag.'/i',
				'/'.$leftTag.'for (.*?)'.$rightTag.'/i',
				'/'.$leftTag.'while (.*?)'.$rightTag.'/i',
				'/'.$leftTag.'(loop|foreach) (.*?) as (.*?)'.$rightTag.'/i',
				'/'.$leftTag.'if (.*?)'.$rightTag.'/i',
				'/'.$leftTag.'else'.$rightTag.'/i',
				'/'.$leftTag."(eval) (.*?)".$rightTag.'/is',
				'/'.$leftTag.'\/(if|for|loop|foreach|while)'.$rightTag.'/i',
				'/'.$leftTag.'((( *(\+\+|--) *)*?(([_a-zA-Z][\w]*\(.*?\))|\$((\w+)((\[|\()(\'|")?\$*\w*(\'|")?(\)|\]))*((->)?\$?(\w*)(\((\'|")?(.*?)(\'|")?\)|))){0,})( *\.?[^ \.]*? *)*?){1,})'.$rightTag.'/i',
				'/'.$leftTag.'\%([\w]+)'.$rightTag.'/',						//多语言
			);
			$replace=array(
				'<?php include($this->template("\\1"));?>',
				$this->leftTag."\\1['\\2']".$this->rightTag,
				"\$this->compile_replace('\\1')",
				'<?php }else if (\\2){ ?>',
				'<?php for (\\1) { ?>',
				'<?php $__i=0; while (\\1) {$__i++; ?>',
				'<?php $__i=0; foreach ((array)\\2 as \\3) { $__i++; ?>',
				'<?php if (\\1){ ?>',
				'<?php }else{ ?>',
				'<?php \\2; ?>',
				'<?php } ?>',
				'<?php echo \\1;?>',
				'<?php echo $this->lang["\\1"];?>',
			);
			$content=preg_replace($search,$replace,$content);
			file_put_contents($compiledFile,$content,LOCK_EX);
		}
		return $compiledFile;
	}
	
	/**
	 * 根据是否使用缓存，输出缓存文件内容
	 *
	 * @param string $tplFile
	 * @param string $cacheID
	 */
	public function cache($tplFile,$cacheID='')
	{
		$this->cacheID=$cacheID;
		$cacheFile=$this->getCacheFileName($file,$cacheID);
		if ($this->cached($file,$cacheID)) {
			readfile($cacheFile);
			exit;
		} elseif ($this->caching) {
			ob_start(array(&$this,'_saveCache'));
			$this->cacheFile=$cacheFile;
		}
	}
	
	/**
	 * 返回编译后的模板文件完整路径
	 *
	 * @param string $file
	 * @return string
	 */
	public function template($file='')
	{
		$file=$this->getTemplateFile($file);
		return $this->compile($file);
	}
	
	/**
	 * 回调函数，供cache()函数使用
	 *
	 * @param string $output
	 * @return string
	 */
	public function _saveCache($output)
	{
		$cacheDir=$this->cacheDir.$this->cacheDirLevel;
		is_dir($cacheDir) or mkdir($cacheDir,0777,true);
		file_put_contents($this->cacheFile,$output,LOCK_EX);
		return $output;
	}
	
}//end class