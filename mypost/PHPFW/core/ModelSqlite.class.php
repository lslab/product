<?php

class db extends ModelSqlite {

	public $tableName='infos';
	public $primaryKey='id';
	public $fields=array('id','username','mobile','content','addtime');
	
}


/**
 * Model模型类
 * 必须手工指定 $fields属性，就是本表所有的字段，格式：一维数组
 */

class ModelSqlite extends PDO 
{
	public $debug=false;
	public $primaryKeys = array();	//设定各表的主键，在创建类实例后必须设定表主键 $db->primaryKeys(array('表1'=>'主键字段1','表2'=>'主键字段2'))
	public $queryCount = 0;
		
	public $tableFields = array();
	
	/////////////////////////////////
	
	public $conn;	//链接
	public $primaryKey	= 'id';		//主键
	public $tableName	= '';		//表名（不包括表前缀）
									
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

	
	/**
	 * 架构函数
	 * @access public
	 * @param mixed $data 要创建的数据对象内容
	 */
	
	public function __construct($dbfile=null)
	{
		//初始化
		if (!$dbfile) {
			$dbfile=App::config('db_file');
		}
		$dsn='sqlite:'.$dbfile;
		$this->conn=new PDO($dsn);

		//完整表名、表主键、表前缀
		$this->primaryKeys[$this->tableName]=$this->primaryKey;
		/**
		 * 转化手工指定的表字段为：DESCRIBE tableName 查询出来的格式
		 * 最佳的方法是缓存 DESCRIBE tableName 查询出来的数据
		 * 这里暂且不使用缓存的方式
		 */
		if (is_array($this->fields) && !empty($this->fields)) {
			foreach ($this->fields as $field) {
				$field=strtolower($field);
				$fields[$field]=array();
			}
			$this->tableFields[$this->tableName]=$fields;
		}
	}
	
	function query($sql)
	{
		if ($this->debug) {
			echo "<p><b>MySQL DEBUG:</b>\n$sql\n</p>";
		}
		$q=$this->conn->query($sql);
		$q->setFetchMode(PDO::FETCH_ASSOC);
		return $q;
	}//query

	/**
	 * 查找单条数据
	 *
	 * @param array/sring $fields 查询字段
	 * @param string/int $conditions 查询条件。当为数值时，就是查询表主键
	 * @param string $sort 排序方法
	 * @return array
	 */
	public function find($fields= '*', $conditions=null, $sort=null)
	{
		$sql = self::buildSelectSQL( $fields, $conditions, $sort, 1);
		return self::findBySql($sql);
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
		$table=$this->tableName;
		$sql = "SELECT " . self::qfield($table, $fields) . " FROM $table";
		$sql.= " WHERE " . $this->primaryKeys[$table] . " = '" . $this->qstr($primaryKey) . "'";
		return self::findBySql($sql);
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
		$row=self::find($field,$conditions,$sort);
		$row=array_change_key_case($row,CASE_LOWER);
		$field=strtolower($field);
		return isset($row[$field]) ? $row[$field] : null;
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
		if ($limit) {
			$limit = (int)$limit;
		}
		if ($limit && $page) {
			$limit = (int)$limit * (int)$page . ',' . (int)$limit;
		}
		$sql = self::buildSelectSQL( $fields, $conditions, $sort, $limit);
		return self::findAllBySql($sql);
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
		$table=strtolower($this->tableName);

		(int)$page < 1 && $page=1;
		$limit = (int)$pagesize * ((int)$page - 1) . ',' . (int)$pagesize;
		$sql = self::buildSelectSQL( $fields, $conditions, $sort, $limit);
		$sqlpage = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) as c FROM ', 
			self::buildSelectSQL($fields, $conditions, $sort, null));
		$c = self::findBySql($sqlpage);
		$result['count'] = $c['c'];	
		$result['rowset'] = self::findAllBySql($sql);
		$result['pagecount'] = ceil($result['count'] / $pagesize);
		return $result;
	}

	/**
	 * 返回符合条件的数据数
	 *
	 * @param string $table
	 * @param string/array $conditions
	 * @return int
	 */
	public function findCount($conditions)
	{
		$table=$this->tableName;
		
		if (is_null($conditions)) {
			$row = self::findBySql("SELECT COUNT(*) AS c FROM $table");			
		} else {
			if (is_array($conditions)) {
				$conditions = self::formatArrayConditon($conditions);
			}
			$row = self::findBySql("SELECT COUNT(*) AS c FROM $table WHERE $conditions");			
		}

		return $row['c'];
	}
	
	/**
	 * 创建（插入）一条数据，会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 *
	 * @param array $rows �多维数组
	 * @return 数据表中�返回插入的主键值
	 */
	public function create($row)
	{
		$q=$this->query($this->buildInsertSQL($row));
		return $q;
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
		return self::query(self::buildUpdateSQL($row, $conditions));
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
	public function delete($conditions)
	{
		return parent::delete($this->tableName,$conditions);
	}
	
	/**
	 * 根据主键删除
	 *
	 * @param string $table
	 * @param mixed $primaryKey
	 * @param int $limit
	 * @return int
	 */
	public function delByPrk($primaryKey)
	{
		$table=$this->tableName;
		$sql = "DELETE FROM $table WHERE " . $this->primaryKeys[$table] . " = '" . $this->qstr($primaryKey) . "'";
		$limit = (int)$limit;
//		if ($limit) { $sql .= " limit $limit"; }
		return self::query($sql);
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
			$this->validator=new ValidatorSqlite();
			$this->validator->checkMode=strtolower($checkMode);
		}
		//获取规则
		if ($this->validateRule==null) {
			$this->validateRule=include(APP_PATH.'/config/forms/'.App::$controller.'_'.App::$action.'.rules.php');
		}
		return $this->validator->validate($data,$this->validateRule);
	}//validate
	
	
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
			
			if (!isset($this->tableFields[$this->tableName])) {
				$this->tableFields[$this->tableName]=parent::getTableFields($this->tableName);
			}
			if (isset($this->tableFields[$this->tableName][$field])) {
				return $this->find('*',array($field=>$value));
			} else {
				return 'no field';
			}
		}
		
		return 'no';
	}//__call
	
	
	//转义相关字符
	public function qstr($str, $addComma = false)
	{
//		$result = self::$conn->quote($str);
		$result=addslashes($str);
		if ($addComma) {
			$result = "'".$result."'";
		}
		return $result;
	}

	
	
	/**
	 * 构建select语句
	 *
	 * @param string $table
	 * @param array $fields
	 * @param string/int/array $conditions
	 * @param string $sort
	 * @param int $limit
	 * @return string
	 */
	public function buildSelectSQL($fields, $conditions=null, $sort=null, $limit=null)
	{
		$table=strtolower($this->tableName);
		
		$sql = "SELECT " . self::qfield($table, $fields) . " FROM $table";
		if (!empty($conditions)) {
			if (is_int($conditions)) {
				$sql .= ' WHERE ' . $this->primaryKeys[$table] . " = '" . self::qstr($conditions) . "'";
			} elseif (is_array($conditions)) {
				$sql .= ' WHERE ' . self::formatArrayConditon($conditions);
			} else {
				$sql .= " WHERE $conditions";
			}
		}
		if ($sort) {
			$sql .= " ORDER BY $sort";
		}
		if ($limit) {
			$sql .= " LIMIT $limit";
		}
		return $sql;
	}
	
	
	/**
	 * 构建insert into 语句
	 *
	 * @param string $table
	 * @param array $row
	 * @return string
	 */
	public function buildInsertSQL($row)
	{
		$table=strtolower($this->tableName);
		$row=array_change_key_case($row);
		
		$fields = self::getTableFields($table);
		
		foreach ($row as $k=>$v) {
			if (isset($fields[$k])) {
				$sqlrow[$k] = "'" . $this->qstr($v) . "'" ;	
			}
		}
		$sql = "INSERT INTO $table (" . $this->qfield($table, array_keys($sqlrow)) . ") VALUES (".
			join(",", $sqlrow) . ");\n";
		return $sql;
	}
	
	/**
	 * 构建 update 语句
	 *
	 * @param string $table
	 * @param array $row
	 * @param string/array $condition
	 * @return string
	 */
	public function buildUpdateSQL($row, $conditions)
	{
		$table=strtolower($this->tableName);
		$row=array_change_key_case($row);

		$fields = self::getTableFields($table);
		
		$sql = "UPDATE $table SET ";
		foreach ($row as $k=>$v) {
			if (isset($fields[$k]) && $k != $this->primaryKeys[$table]) {
				$sql .= "$k = '" . $this->qstr($v) . "'," ;	
			}
		}//foreach
		$sql = substr($sql, 0, strlen($sql)-1) . ' WHERE 1';
		if (isset($row[$this->primaryKeys[$table]])) {
			$sql .= " AND " . $this->primaryKeys[$table] . " = '" .
				$this->qstr($row[$this->primaryKeys[$table]]) . "'";
		}
		
		if (is_array($conditions)) {
			$sql .= ' AND ' . self::formatArrayConditon($conditions);
		} elseif ($conditions) {
			$sql .= " AND $conditions";
		}
		return $sql;
	}
	
	/**
	 * 构建delete语句
	 *
	 * @param string $table
	 * @param string/array $conditions
	 * @param int $limit
	 * @return string
	 */
	public function buildDeleteSQL($table, $conditions, $limit = null)
	{
		$table=strtolower($table);

		$sql = "DELETE FROM $table";
		if ($conditions) {
			if (is_int($conditions)) {
				$sql .= " WHERE " . $this->primaryKeys[$table] . " = '" . $this->qstr($conditions) . "'";
			} elseif (is_array($conditions)) {
				$sql .= ' WHERE ' . self::formatArrayConditon($conditions);
			} else {
				$sql .= ' WHERE ' . $conditions;
			}
		}//if $conditions
		if ($limit) {
			$sql .= " LIMIT " . (int)$limit;
		}
		return $sql;
	}
	
	//私有方法，格式化字段，将 field 变为 table.field
	private function qfield($table, $fields)
	{
		$table=strtolower($table);

		$table = "";
		!$fields && $fields = '*';
		if ('*' != $fields) {
			if (!is_array($fields)) {
				$fields = explode(',', $fields);
			}
			$fields=array_change_key_case($fields);
			foreach ($fields as $field) {
				$field = trim($field);
				if (!preg_match('/\s/', $field)) {
					$result[] = $table . $field ;
				} else {
					$result[] = $table . $field;
				}
			}
			
		} else {
			$result[] = $table . $fields;
		}
		return implode(',', $result);
	}	
	
	/**
	 * 将数组条件转化为可以直接使用的条件
	 *
	 * @param array $arrConditon
	 * @return string
	 */
	private function formatArrayConditon($arrConditon)
	{
		$array=array();
		foreach ($arrConditon as $field=>$value) {
			$array[]=$field.'='.self::qstr($value,true);
		}
		return implode(' AND ',$array);
	}
	
	//私有方法，获取表中字段的信息，返回多维数组
	protected function getTableFields($table)
	{
		$r=array();
		foreach ($this->fields as $f) {
			$r[trim($f)]='';
		}
		return $r;
	}
	
	
	/**
	 * 直接使用SQL语句查询，返回单条数据，不会自动处理表前缀、字符转义等工作
	 *
	 * @param string $sql
	 * @return 返回一维数组
	 */
	public function findBySql ($sql) 
	{
		$q = $this->query($sql);
		return $q->fetch();
	}

	
	/**
	 * 直接使用SQL语句查询，返回多条数据，不会自动处理表前缀、字符转义等工作
	 *
	 * @param string $sql
	 * @return array
	 */
	public function findAllBySql ($sql) 
	{
		$q=$this->query($sql);
		return $q->fetchAll();
	}
	
}//class
?>