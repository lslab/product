<?php
/**
 * Model模型类
 */

class Model extends DBase 
{	
	public $primaryKey	= 'id';		//主键
	public $tableName	= '';		//表名（不包括表前缀）
	public $tablePrefix	= null;		//让用户设置的表前缀，如果此值为NULL就使用app::$config中的值，
									//不为Null（包括空串）时就使用用户设置的值
									
	protected $trueTableName ='';	//string 完整的表名
	protected $validator=null;		//object 数据验证类实例
	protected $fields;				//数据表的所有字段，例子：$this->fields=array('id','name','age');
	/**
	 * 插入数据时自动填充字段
	 * 举例：$autofields=array(
	 * 		array('field1','time',1,'string',1),			
	 * 		array('field2','myfunction',2,'function',1),
	 * 		array('field3','mymethod',3,'callback',1),
	 * 		array('field4','field5',1,'field',2),
	 * );
	 * 以上多维数组其中每维各个值的意思是：
	 * 1、字段名
	 * 2、动作名
	 * 3、模式（默认为1代表插入时填充，2代表更新时填充，3代表插入、更新时都填充）
	 * 4、动作名 的类型（默认为string表示固定字符串，function表示“2、动作名”是一个函数，callback表示是本类的一个方法名，field表示从其它字段复制值
	 * 5、自动填充模式：如果为1，表示在提供给autoField()方法中要求填充的数组包含此字段就填充，不包含就不处理；如果为2，表示不管是否包含都填充；默认为1
	 *
	 * @var array
	 */
	protected $autoFields;			
	
	/**
	 * 架构函数
	 * @access public
	 * @param mixed $data 要创建的数据对象内容
	 */
	
	public function __construct()
	{
		//初始化
		parent::__construct(
			App::config('db_host'),
			App::config('db_user'),
			App::config('db_password'),
			App::config('db_database'),
			App::config('db_charset'),
			App::config('db_pconnect')
		);
		//完整表名、表主键、表前缀
		$tablePrefix=($this->tablePrefix!==null) ? $this->tablePrefix : App::config('db_table_prefix');
		$this->trueTableName=$tablePrefix.$this->tableName;
		$this->primaryKeys[$this->trueTableName]=$this->primaryKey;
		/**
		 * 转化手工指定的表字段为：DESCRIBE tableName 查询出来的格式
		 * 最佳的方法是缓存 DESCRIBE tableName 查询出来的数据
		 * 这里暂且不使用缓存的方式
		 */
		if (is_array($this->fields) && !empty($this->fields)) {
			foreach ($this->fields as $field) {
				$fields[$field]=array();
			}
			$this->tableFields[$this->trueTableName]=$fields;
		}
	}

	/**
	 * 查找单条数据
	 *
	 * @param array/sring $fields 查询字段
	 * @param string/int $conditions 查询条件。当为数值时，就是查询表主键
	 * @param string $sort 排序方法
	 * @return array
	 */
	public function find ($fields= '*', $conditions=null, $sort=null)
	{
		return parent::find($this->trueTableName,$fields,$conditions,$sort);
	}
	
	/**
	 * 根据主键查询
	 *
	 * @param mixed $primaryKey
	 * @param string/array $fields
	 * @return array
	 */
	public function findByPrk($primaryKey, $fields='*')
	{
		return parent::findByPrk($this->trueTableName,$primaryKey,$fields);
	}//findByPrk
	
	/**
	 * 查找某个字段的值
	 *
	 * @param string $field
	 * @param string $conditions
	 * @param string $sort
	 * @return string
	 */
	public function findField($field, $conditions=null, $sort=null)
	{
		return parent::findField($this->trueTableName,$field,$conditions,$sort);
	}//findField

	/**
	 * 查询多条数据
	 *
	 * @param string $fields
	 * @param string|int $conditions
	 * @param string $sort
	 * @param int $limit 限制查询数据条数
	 * @param int $page 分页查询时设定第N页，分布查询请使用findPage()
	 * @return 多维数组
	 */
	public function findAll ($fields= '*', $conditions=null, $sort=null, $limit=null, $page=null) 
   	{
   		return parent::findAll($this->trueTableName, $fields, $conditions, $sort, $limit, $page);
   	}

	/**
	 * 分页查找
	 *
	 * @param string $fields
	 * @param string $conditions
	 * @param string $sort
	 * @param int $page 第N页
	 * @param int $pagesize 每页返回的数量数
	 * @return 多维数组
	 */
   	public function findPage ($fields= '*', $conditions=null, $sort=null, $page, $pagesize) 
	{
		return parent::findPage($this->trueTableName, $fields, $conditions, $sort, $page, $pagesize);
	}

	/**
	 * 返回符合条件的数据数
	 *
	 * @param string $conditions
	 * @return int
	 */
	public function findCount($conditions=null)
	{
		return parent::findCount($this->trueTableName, $conditions);
	}
	
	/**
	 * 创建（插入）一条数据，会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 *
	 * @param array $rows �多维数组
	 * @return 数据表中�返回插入的主键值
	 */
	public function create($row)
	{
		$row=$this->autoFields($row,'insert');
		return parent::create($this->trueTableName,$row);
	}
	
	/**
	 * 更新单条数据，会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 *
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return 返回数据表受影响的行数
	 */
	public function update($row,$conditions=null)
	{
		$row=$this->autoFields($row,'update');
		return parent::update($this->trueTableName,$row,$conditions);
	}

	/**
	 * 根据$row数组中是否含有表主键值来创建或更新数据，$row中含有表主键值执行update()操作，反之执行create()操作，是create()与update()的结合体
	 * 会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 * @param array $row
	 * @param string $conditions
	 * @return 根据是创建还是更新返回数据表插入的主键值或受影响的行数
	 */
	public function save($row, $conditions=null)
	{
		if (isset($row[$this->primaryKey])) {
			return $this->update($row, $conditions);
		} else {
			return $this->create($row);
		}
	}

	/**
	 * 删除符合条件的数
	 *
	 * @param string $conditions
	 * @param int $limit 限制删除数据的数组
	 * @return 返回删除后受影响的行数
	 */
	public function delete($conditions, $limit = null)
	{
		return parent::delete($this->trueTableName,$conditions,$limit);
	}
	
	/**
	 * 根据主键删除
	 *
	 * @param mixed $primaryKey
	 * @param int $limit
	 * @return int
	 */
	public function delByPrk($primaryKey, $limit=1)
	{
		return parent::delByPrk($this->trueTableName, $primaryKey, $limit);
	}//delByPrk
	
	/**
	 * 验证数据是否合法
	 *
	 * @param array $data
	 * @param string $checkMode		可取值：all/all_one/skip
	 * @return array|null
	 */
	public function validate($data, $checkMode='all_one')
	{
		if ($this->validator==null) {
			$this->validator=new Validator();
			$this->validator->checkMode=strtolower($checkMode);
		}
		//获取规则
		if ($this->validateRule==null) {
			$this->validateRule=include(APP_PATH.'/config/forms/'.App::$controller.'_'.App::$action.'.rules.php');
		}
		return $this->validator->validate($data,$this->validateRule);
	}//validate
	
	/**
	 * 将自动填充字段的值插入到欲插入、更新的数据（数组）中
	 *
	 * @param array $rows
	 * @param string $mode	(insert/update)
	 * @return array
	 */
	protected function autoFields($row,$mode)
	{
		$modeCode=$mode=='insert'?1:2;
		foreach ((array)$this->autoFields as $rule) {
			list($field,$action,$ruleModeCode,$type,$autoMode)=$rule;
			$ruleModeCode or $ruleModeCode=1;
			$type or $type='string';
			$type=strtolower($type);
			$autoMode!==2 && $autoMode=1;

			if ($ruleModeCode==3 || $modeCode==$ruleModeCode) {
				$param = isset($row[$field]) ? $row[$field] : null;
				switch ($type) {
					case 'string':
						$value=$action;
						break;
					case 'function':
//						$value=$param==null ? $action() : $action($param);
						$value=$action($param);
						break;
					case 'callback':
//						$value=$param==null ? $this->$action() : $this->$action($param);
						$value=$this->$action($param);
						break;
					case 'field':
						$value=$row[$field];
						break;
					#################以下为别名############
					case 's':
						$value=$action;
						break;
					case 'f':
						$value=$action($param);
						break;
					case 'c':
						$value=$this->$action($param);
						break;
					case 'fi':
						$value=$row[$field];
						break;
				}
				if (($autoMode==1 && isset($row[$field])) || $autoMode==2) {
					$row[$field]=$value;
				}
			}
		}

		return $row;
	}//autoFields
	
	/**
	 * 魔术方法
	 *
	 * @param string $name
	 * @param array $params
	 */
	public function __call($name,$params)
	{
		//findByXxx 魔术查询方法
		if (preg_match('/^findBy(\w+)$/i',$name,$array)) {
			$field=strtolower($array[1]);
			$value=$params[0];
			
			if (!isset($this->tableFields[$this->trueTableName])) {
				$this->tableFields[$this->trueTableName]=parent::getTableFields($this->trueTableName);
			}
			if (isset($this->tableFields[$this->trueTableName][$field])) {
				return $this->find('*',array($field=>$value));
			} else {
				return 'no field';
			}
		}
		
		return 'no';
	}//__call
	
}//class
?>