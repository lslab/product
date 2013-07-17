<?php
/**
 * 应用程序类 执行应用过程管理
 */

class App extends Base
{//类定义开始

	public static $controller;
	public static $action;
	public static $includeFiles=array();	//整个框架包含的文件
	public static $globals=array();			//保存全局变量
	private static $_config;

	public function __construct($config=array())
	{
		
		//设置
		self::$_config=require(PHPFW_PATH.'/config/common.php');
		if (empty($config)) { $config=require(APP_PATH.'/config/common.php'); }
		//适当处理某些设置的值
		$config['cache_dir']=strlen($config['cache_dir']) ? rtrim($config['cache_dir'],'/') : APP_PATH.'/cache';
		//保存设置至app类属性
		foreach ((array)$config as $key=>$val) {
			self::$_config[$key]=$val;
		}
		//解析controller和action
		if (isset($_GET['r'])) {
			list($ctr,$act)=explode('.',$_GET['r'],2);
		} else {
			$ctr=$act=null;
		}
		
		self::$controller	= $ctr ? $ctr : self::config('default_controller');
		self::$action		= $act ? $act : self::config('default_action');
		
		date_default_timezone_set(self::config('timezone'));
		
	}
	
	/**
	 * 获取配置信息
	 *
	 * @param string $key
	 * @return mixed
	 */
	public static function config($key=null)
	{
		if (null===$key) {
			return App::$_config;
		} elseif (isset(App::$_config[$key])) {
			return App::$_config[$key];
		} else {
			return null;
		}
	}

	/**
	 * 运行应用实例
	 * @access public
	 * @return void
	 */
	public function run() 
	{
		//引入编译、缓存过的引入文件
		$compiledIncFile=$this->getCompiledIncFileName();
		if (App::config('compile_include_files') && is_file($compiledIncFile)) {
			self::$includeFiles=require($compiledIncFile);
		}
		//检测控制器文件是否存在
		if (!is_file(APP_PATH.'/controller/'.self::$controller.'Controller.class.php')) {
			die("<h1>Invalid Request</h1>\nController <strong>".self::$controller."</strong> not found.");
		}
		//导入必需文件
		irequire(PHPFW_PATH.'/common/common.php');
		is_file(APP_PATH.'/common/common.php') && irequire(APP_PATH.'/common/common.php');
		irequire(PHPFW_PATH.'/core/Controller.class.php');
		irequire(APP_PATH.'/controller/'.self::$controller.'Controller.class.php');
		//实例化控制器并运行
		$controllerName=self::$controller.'Controller';
		$controller=new $controllerName();
		$controller->run(self::$action);

		//编译、缓存 引入文件
		if (App::config('compile_include_files') && !is_file($compiledIncFile)) {
			$this->compileIncFiles();
		}
		
	}
	
	/**
	 * 将导入的文件编译为一个文件，节省IO消耗时间
	 *
	 */
	private function compileIncFiles()
	{
		$content='<?php';
		$tmpCount=count(App::$includeFiles)-1;
		for ($i=$tmpCount; $i>=0; $i--) {
			$content.=$this->compile(App::$includeFiles[$i]);
		}
		
		$content.="?><?php\nreturn ";
		$content.=var_export(App::$includeFiles,true);
		$content.=";\n?>";
		
		file_put_contents($this->getCompiledIncFileName(),$content);
	}

	//编译文件，去除注释、空白……
	private function compile($filename) 
	{
		$content = php_strip_whitespace($filename);
		$content = substr(trim($content),5);
		if('?>' == substr($content,-2)) {
			$content = substr($content,0,-2);
		}
		return $content;
	}
	
	//返回引入文件缓存文件名
	private function getCompiledIncFileName() 
	{
		return self::config('cache_dir') . '/' . self::$controller . '_' . self::$action . '.IncludeComplied.php';
	}
	

}//类定义结束
?>