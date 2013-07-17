<?php
/**
 * Action控制器基类 抽象类
 */

abstract class Controller extends Base
{
	//语言包
	public $lang = array();
	//default lang
	public $defaultLanguage=null;
	//视图对象（模板类）
	protected $view = null;
	//保存已经实例化的model类
	protected $models=array();
	//实例化的db类
	protected $db = null;
	
   /**
	 * 架构函数
	 * @access public
	 */
	public function __construct()
	{
		//自动加载语言包
		$this->autoloadLanguageFile();
	}
	
	/**
	 * 未知的 action
	 *
	 */
	public function __call($method,$params)
	{
		echo "<h1>Invalid Request</h1>\n";
		echo "Action <strong>$method</strong> from ".App::$controller." not found.\n";
		exit;
	}
	
	/**
	 * 未知的属性
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		//可以直接使用模块名称作为controller的属性来访问
		$modelFile=$name.'Model.class.php';
		if (is_file(APP_PATH."/model/$modelFile")) {
			return $this->model($name);
		}
	}//__get
	
	/**
	 * 自动在其它action前运行
	 * 当该方法返回值为false时，将不会执行当前的action方法，但是 _afterAction()无论如何都会运行
	 */
	protected function _beforAction() {}
	
	//自动在其它action运行完后运行
	protected function _afterAction() {}
	
	/**
	 * 注册类文件路径
	 *
	 * @param array|string $className
	 * @param string $classFile
	 */
	public function regClassFile($className,$classFile=null)
	{
		if (is_array($className)) {
			foreach ($className as $key => $value) {
				$this->regClassFile($key,$value);
			}
		} else {
			App::$globals['classFiles'][$className]=$classFile;
		}
	}
	
	/**
	 * 加载模型对象
	 *
	 * @param string $modelName
	 * @param boolean $isFullModelName
	 * @return object
	 */
	public function model($modelName,$isFullModelName=false)
	{
		$isFullModelName or $modelName.='Model';
		if (isset($this->models[$modelName])) {
			return $this->models[$modelName];
		} else {
			$modelFile=APP_PATH.'/model/'.$modelName.'.class.php';
			if (!is_file($modelFile)) {
				die("<h1>Invalid Request</h1>\nModel <strong>$modelName</strong> not found!");
			}
			irequire($modelFile);
			$m=new $modelName();
			$this->models[$modelName]=$m;
			return $m;
		}
	}
	
	public function db()
	{
		if ($this->db===null) {
			$this->db=new DB();
		}
		return $this->db;
	}//db
	
	/**
	 * 返回视图对象
	 *
	 * @return object
	 */
	public function view()
	{
		if ($this->view===null) {
			$options=array(
				'templateDir'		=> APP_PATH.'/view',
				'compileDir'		=> App::config('cache_dir'),
				'cacheDir'			=> App::config('cache_dir'),
				'cacheDirLevels'	=> App::config('page_cache_dir_levels'),
				'caching'			=> App::config('page_caching'),
				'cacheLifeTime'		=> App::config('page_cache_life_time'),
				'templateFileExt'	=> App::config('template_file_ext'),
			);
			$this->view=new View($options);
			//加载语言包
			$this->view->lang=$this->lang;
		}
		return $this->view;
	}

	/**
	 * 读取、输出页面缓存
	 *
	 * @param string $tplFile
	 * @param string $cacheID
	 */
	public function fullPageCache($tplFile='',$cacheID='')
	{
		$this->view()->cache($tplFile,$cacheID);
	}
	
	/**
	 * 返回编译后的模板文件路径
	 *
	 * @param string$file
	 * @return string
	 */
	public function template($file='')
	{
		return $this->view()->template($file);
	}

	/**
	 * 运行控制器
	 *
	 * @param string $action
	 */
	public function run($action)
	{
		$this->_beforAction();
		$this->{$action.'Action'}();
		$this->_afterAction();
	}
	
	/**
	 * 计算程序运行使用时间
	 *
	 * @return float
	 */
	public function expendTime()
	{
		return round(microtime(true)-App::$globals['beginTime'],6);
	}
	
	/**
	 * URL重定向
	 *
	 * @param string $url
	 * @param int $time
	 * @param string $msg
	 */
	public function redirect($url,$time=0,$msg='')
	{
		//多行URL地址支持
		$url = str_replace(array("\n", "\r"), '', $url);
		if (!headers_sent()) {
			// redirect
			if(0===$time) {
				header("Location: ".$url);
			}else {
				header("refresh:{$time};url={$url}");
				echo($msg);
			}
		} elseif ($time>0) {
			$str	= "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
			$str .= $msg;
			echo $str;
		} else {
			echo "<script type=\"text/javascript\">setTimeout(\"window.location.href='{$url}',1\");</script>";
		}
		exit;
	}
	
	/**
	 * 自动加载语言包
	 *
	 */
	private function autoloadLanguageFile() {
		$defsultLanguage=$this->defaultLanguage ? $this->defaultLanguage : App::config('default_language');
		$langfile=APP_PATH.'/lang/'.$defsultLanguage.'/'.App::$controller.'_'.App::$action.'.php';
		if (App::config('autoload_language_file') && is_file($langfile)) {
			$this->lang=include($langfile);
		}
	}
	
	public function setDefaultLanguage($lang)
	{
		$this->defaultLanguage=$lang;
	}//setDefaultLanguage
	
}//类定义结束
?>