<?php
/**
 * 系统基类 抽象类
 */

abstract class Base
{
	protected $_vars = array();
	/**
	 * 自动设置变量
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function __set($name ,$value)
	{
		$this->_vars[$name] = $value;
	}

	/**
	 * 自动获取变量
	 *
	 * @param string $name
	 */
	public function __get($name)
	{
		if(isset($this->_vars[$name])){
			return $this->_vars[$name];
		}else {
			return null;
		}
	}
	
}//类定义结束
?>