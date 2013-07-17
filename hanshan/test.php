<?php
class Template {
	public $cacheDir = './cache/';
	public $templateDir = './templates/';
	public $caching = false;
	public $cacheLifetime = 3600;
	public $suffix = '.phpx';
	public $ereg = '/^\w+$/';
	public $tplFile = '';
	public $tplColl = array();
	public $cacheFile = '';
	function __construct($array = array()) {
	}
	function __get($property) {
	}
	function assign($name, $value = '') {
		if(is_array($name)) {
			foreach($name as $k=>$v) {
				$this->assign($k, $v);
			}
		}
		else {
			$GLOBALS[$name] = $value;
		}
		return $this;
	}
	function setCacheLifetime($time = 0) {
		$this->cacheLifetime = $time;
		$time==-1 && $time=3600*24*14;
		$GLOBALS['CFG']['font_cache'] && @ header("Cache-control: max-age=$time");
		return $time;
	}
	function __call($name, $arguments) {
		Lamson::__call($name, $arguments+array('this'=>$this));
		return $this;
	}
	private function _getDir($dir) {
		$dir = !empty($this->$dir) ? $this->$dir : $dir;
		substr($dir, -1)!='/' && ($dir .= '/');
		return $dir;
	}
	protected function _setCacheName($filename, $cacheid = NULL) {
		return ($cacheid ? $this->hashId($cacheid) .'^' : '') . abs(crc32($filename)). '.' . basename($filename, TPLSUF) . $this->suffix;
	}
	protected function _tplFileName($filename = NULL) {
		return $filename . TPLSUF;
	}
	function includeTpl($filename) {
		return $this->_getDir('templateDir') . ($this->tplColl[] =$this->_tplFileName($filename));
	}
	function hashId($str = NULL, $res = false) {
		return isset($str) ? ( strtolower( (!$res && (!preg_match($this->ereg, $str) || strlen($str)>10) ) ? abs(crc32($str)) : $str ) ) : NULL;
	}
	function isCached($filename, $cacheid = NULL) {
		global $tpleng;
		$this->cacheFile = $this->_getDir('cacheDir') . $this->_setCacheName($filename, $cacheid);
		if($this->caching && file_exists($this->cacheFile)) {
			$no_render = true;
			include($this->cacheFile);
		}
		return $no_render && ($this->cacheLifetime==-1 ? true : ($_SERVER['REQUEST_TIME'] < (filemtime($this->cacheFile) + $this->cacheLifetime))) && $this->_isTplsChange($this->tplFile);
	}
	protected function _isTplsChange($tpls) {
		foreach((array)$tpls as $k=>$v) {
			if( (is_file($f=$this->_getDir('templateDir') . $k) ) && @ filemtime($f) != $v) {
				return false;
			}
		}
		return true;
	}
	function fetch($filename, $output = false) {
		$this->tplFile = $this->_getDir('templateDir') . $filename;
		if(!is_file($this->tplFile)) {
			die("<p style='text-align:center;
'>模版 <b>$this->tplFile</b> 无法读取！</p>");
		}
		extract($GLOBALS);
		if($output) {
			include $this->tplFile;
			die;
		}
		$this->tplColl[] = $filename;
		ob_start();
		include $this->tplFile;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	function display($filename = NULL, $cacheid = NULL) {
		if( $this->isCached($filename, $cacheid) ) {
			return $this->cacheFile;
		}
		if($this->caching) {
			$content = $this->fetch( $this->_tplFileName($filename), !$this->caching );
			$phpstr = '<?php $tpleng->tplFile=array(';
			foreach($this->tplColl as $v) {
				$phpstr .= "'$v'=>".filemtime($this->_getDir('templateDir') . $v).',';
			}
			$phpstr = substr($phpstr, 0, -1).');
 ?>';
			$this->save($phpstr.chr(13).chr(10).'<?php if(!$no_render){
?>'.$content.'<?php }
?>');
			die($content);
		}
		return $this->includeTpl($filename);
	}
	function save($content = '', $in_charset = 'utf-8', $out_charset = 'utf-8') {
		$content = $in_charset!=$out_charset ? iconv($in_charset, "$out_charset//TRANSLIT", $content) : $content;
		!is_dir($this->cacheDir) && mkdir($this->cacheDir, 0777, true);
		@ file_put_contents($this->cacheFile, $content, LOCK_EX);
		@ chmod($this->cacheFile, 0777);
	}
	function fastClearCache($tpl, $dir = '') {
		$dir = $dir ? $dir : $this->_getDir( 'templateDir' );
		settype($tpl, 'array');
		foreach($tpl as $v) {
			$v = $dir.$this->_tplFileName($v);
			if(is_file($v) && !touch($v)) {
				$this->_error .= "<li>$v</li>";
				file_put_contents($v, file_get_contents($v).' ');
			}
		}
		return $this;
	}
	function clearCache($filename = NULL, $cacheid = NULL, $dir = '') {
		if(!isset($filename)) {
			File::delDir($this->cacheDir, true);
		}
		else {
			if(!isset($cacheid)) {
				$this->fastClearCache($filename, $dir);
			}
			else {
				@ unlink( $this->_getDir('cacheDir') . $this->_setCacheName($filename, $cacheid) );
			}
		}
		return $this;
	}
	function getError() {
		return $this->_error ? "<p>无法设定以下文件的访问和修改时间：<ul> $this->_error　</ul></p>" : '';
	}
}
