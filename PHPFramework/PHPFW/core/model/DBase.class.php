<?php
/**
 * 操作mysql的基础类，其它与mysql有关的类都继承于此基类
 * 
 * 此class中的$table都是已经包含表前缀的完整表名
 * 
 * ver 20090717
 * 使用范例
 * $db = new DB('localhost','root','password','database','utf8');
 * $db->debug = true;
 * $db->primaryKeys = array (
 *	'table_1' => 'id',
 *	'table_2' => 'id2'
 * );
 * $db->find('table_1', 1); 
 * $db->findAll('table_2', '*', 'catid=1', 'id desc');
 * ……
 */


class DBase extends Base {

	public $debug;	//调试
	public $primaryKeys = array();	//设定各表的主键，在创建类实例后必须设定表主键 $db->primaryKeys(array('表1'=>'主键字段1','表2'=>'主键字段2'))
	public $queryCount = 0;
	public $haltOnError = true;
	public $displayError=true;
		
	public $queryID;
	public $connected = false;
	public $tableFields = array();
	public $dbhost;
	public $dbuser;
	public $dbpwd;
	public $dbname;
	public $dbcharset;
	public $pconnect = false;

	public function __construct($host, $user, $pwd, $database = null, $charset = null, $pconnect = null) {
		$this->dbhost = $host;
		$this->dbuser = $user;
		$this->dbpwd  = $pwd;
		$this->dbname = $database;
		$this->dbcharset = $charset;
		$this->pconnect = $pconnect;
		
		//小写
		foreach ($this->primaryKeys as $table=>$field) {
			$primaryKeys[strtolower($table)]=strtolower($field);
		}
		$this->primaryKeys=$primaryKeys;
	}
	
	public function connect()
	{
		if ($this->pconnect) {
			@mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpwd) || die('<b>MySQL ERROR:</b> ' . mysql_error());
		} else {
			@mysql_connect($this->dbhost, $this->dbuser, $this->dbpwd) || die('<b>MySQL ERROR:</b> ' . mysql_error());
		}
		if ($this->dbname != null) {
			$this->selectdb($this->dbname);
		}

		$this->connected = true;
	}

	public function selectdb($dbname) {
		$this->dbname = $dbname;
		mysql_select_db($this->dbname) || die('<b>MySQL ERROR(mysql_selectt_db):</b> ' . mysql_error());
		$serverVersion=$this->serverVersion();
		if ($this->dbcharset && $this->serverVersion()>='4.1') {
			if ( function_exists('mysql_set_charset') )
				mysql_set_charset($this->dbcharset) || die('<b>MySQL ERROR:</b> ' . mysql_error());
			else
				mysql_query("SET NAMES " . $this->dbcharset) || die('<b>MySQL ERROR:</b> ' . mysql_error());
		}
	}
	
	/**
	 * 查询MYSQL版本，以确定是否要用set names xxx
	 *
	 * @return unknown
	 */
	private function serverVersion()
	{
		$qid=mysql_query('select version()');
		$row=mysql_fetch_array($qid);
		return $row[0];
	}

	/**
	 * 运行sql语句
	 *
	 */
	public function query($sql)
	{
		if ($this->debug) {
			echo "<p><b>MySQL DEBUG:</b> $sql</p>";
		}
		if (!$this->connected) {
			$this->connect();
		}
		$this->queryID = @mysql_query($sql);
		if (!$this->queryID) {
			if ($this->displayError) {
				echo '<p><b>MySQL DEBUG:</b> ' . $sql . '</p>';
				echo '<p><b>MySQL ERROR:(' . mysql_errno() . ')</b> ' . mysql_error() . '</p>';
			}
			$this->haltOnError && die("\nmySQL error and session halted\n");
		}
		$this->queryCount++;
		return $this->queryID;
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
	public function buildSelectSQL($table, $fields, $conditions, $sort, $limit)
	{
		$table=strtolower($table);
		
		$sql = "SELECT " . self::qfield($table, $fields) . " FROM `$table`";
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
	public function buildInsertSQL($table, $row)
	{
		$table=strtolower($table);
		$row=array_change_key_case($row);
		
		$fields = self::getTableFields($table);
		
		foreach ($row as $k=>$v) {
			if (isset($fields[$k])) {
				$sqlrow[$k] = "'" . $this->qstr($v) . "'" ;	
			}
		}
		$sql = "INSERT INTO `$table` (" . $this->qfield($table, array_keys($sqlrow)) . ") VALUES (".
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
	public function buildUpdateSQL($table, $row, $conditions)
	{
		$table=strtolower($table);
		$row=array_change_key_case($row);

		$fields = self::getTableFields($table);
		
		$sql = "UPDATE `$table` SET ";
		foreach ($row as $k=>$v) {
			if (isset($fields[$k]) && $k != $this->primaryKeys[$table]) {
				$sql .= "`$k` = '" . $this->qstr($v) . "'," ;	
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

		$sql = "DELETE FROM `$table`";
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
	
	//转义相关字符
	public function qstr($str, $addComma = false)
	{
		if (!$this->connected) {
			$this->connect();
			$this->connected = true;
		}
		$result = mysql_real_escape_string($str);
		if ($addComma) {
			$result = "'".$result."'";
		}
		return $result;
	}
	
	//私有方法，格式化字段，将 field 变为 `table`.`field`
	private function qfield($table, $fields)
	{
		$table=strtolower($table);

		$table = "`$table`.";
		!$fields && $fields = '*';
		if ('*' != $fields) {
			if (!is_array($fields)) {
				$fields = explode(',', $fields);
			}
			$fields=array_change_key_case($fields);
			foreach ($fields as $field) {
				$field = trim($field);
				if (!preg_match('/\s/', $field)) {
					$result[] = $table . '`' . $field . '`';
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
	 * 上次插入数据时自动ID
	 *
	 * @return int
	 */
	public function insertId()
	{
		return mysql_insert_id();
	}//liastID
	
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
	
	/**
	 * 查找单条数据
	 *
	 * @param string $table 表名
	 * @param array/sring $fields 查询字段
	 * @param string/int/array $conditions 查询条件。当为数值时，就是查询表主键值
	 * @param string $sort 排序方法
	 * @return array
	 */
	public function find ($table, $fields= '*', $conditions=null, $sort=null) 
	{
		$sql = self::buildSelectSQL($table, $fields, $conditions, $sort, 1);
		return self::findBySql($sql);
	}
	
	/**
	 * 根据主键查询
	 *
	 * @param string $table
	 * @param mix $primaryKey
	 * @param string/array $fields
	 * @return array
	 */
	public function findByPrk($table, $primaryKey, $fields='*')
	{
		$sql = "SELECT " . self::qfield($table, $fields) . " FROM `$table`";
		$sql.= " WHERE " . $this->primaryKeys[$table] . " = '" . $this->qstr($primaryKey) . "'";
		return self::findBySql($sql);
	}//findByPid
	
	/**
	 * 查找某个字段的值
	 *
	 * @param string $table
	 * @param string $field
	 * @param string/array $conditions
	 * @param string $sort
	 * @return mixed
	 */
	public function findField($table, $field, $conditions=null, $sort=null)
	{
		$table=strtolower($table);
		$field=strtolower($field);

		$row=self::find($table,$field,$conditions,$sort);
		return isset($row[$field]) ? $row[$field] : null;
	}//findField
	
	/**
	 * 查询多条数据
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string/int/array $conditions
	 * @param string $sort
	 * @param int $limit 限制查询数据条数
	 * @param int $page 分页查询时设定第N页，分布查询请使用findPage()
	 * @return 多维数组
	 */
	public function findAll ($table, $fields= '*', $conditions=null, $sort=null, $limit=null, $page=null) 
	{
		if ($limit) {
			$limit = (int)$limit;
		}
		if ($limit && $page) {
			$limit = (int)$limit * (int)$page . ',' . (int)$limit;
		}
		$sql = self::buildSelectSQL($table, $fields, $conditions, $sort, $limit);
		return self::findAllBySql($sql);
	}
	
	/**
	 * 分页查找
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string/array $conditions
	 * @param string $sort
	 * @param int $page 第N页
	 * @param int $pagesize 每页返回的数量数
	 * @return 多维数组
	 */
	public function findPage ($table, $fields= '*', $conditions=null, $sort=null, $page, $pagesize) 
	{
		$table=strtolower($table);

		(int)$page < 1 && $page=1;
		$limit = (int)$pagesize * ((int)$page - 1) . ',' . (int)$pagesize;
		$sql = self::buildSelectSQL($table, $fields, $conditions, $sort, $limit);
		$sqlpage = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) as c FROM ', 
			self::buildSelectSQL($table, $fields, $conditions, $sort, null));
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
	public function findCount($table, $conditions)
	{
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
	 * 直接使用SQL语句查询，返回单条数据，不会自动处理表前缀、字符转义等工作
	 *
	 * @param string $sql
	 * @return 返回一维数组
	 */
	public function findBySql ($sql) 
	{
		$r = mysql_fetch_assoc($this->query($sql));
		mysql_free_result($this->queryID);
		return $r;
	}
	
	/**
	 * 直接使用SQL语句查询，返回多条数据，不会自动处理表前缀、字符转义等工作
	 *
	 * @param string $sql
	 * @return array
	 */
	public function findAllBySql ($sql) 
	{
		self::query($sql);
		while( $row = mysql_fetch_assoc($this->queryID)) {
			$rows[] = $row; 
		}
		mysql_free_result($this->queryID);
		return $rows;
	}
	
	/**
	 * 创建（插入）一条数据，会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 *
	 * @param string $table
	 * @param array $row �多维数组
	 * @return 数据表中�返回插入的主键值
	 */
	public function create($table, $row)
	{
		self::query($this->buildInsertSQL($table,$row));
		return mysql_affected_rows();
	}
	
	/**
	 * 更新单条数据，会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 *
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return 返回数据表受影响的行数
	 */
	public function update($table,$row,$conditions=null)
	{
		self::query(self::buildUpdateSQL($table, $row, $conditions));
		return mysql_affected_rows();
	}
	
	/**
	 * 根据$row数组中是否含有表主键值来创建或更新数据，
	 * $row中含有表主键值执行update()操作，反之执行create()操作，是create()与update()的结合体
	 * 会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return 根据是创建还是更新返回数据表插入的主键值或受影响的行数
	 */
	public function save($table, $row, $conditions=null)
	{
		if (isset($row[$this->primaryKeys[$table]])) {
			return self::update($table, $row, $conditions);
		} else {
			return self::create($table, $row);
		}
	}
	
	/**
	 * 删除符合条件的数
	 *
	 * @param string $table
	 * @param string $conditions
	 * @param int $limit 限制删除数据的数组
	 * @return 返回删除后受影响的行数
	 */
	public function delete($table, $conditions, $limit = null)
	{
		self::query($this->buildDeleteSQL($table, $conditions, $limit));
		return mysql_affected_rows();
	}
	
	/**
	 * 根据主键删除
	 *
	 * @param string $table
	 * @param mixed $primaryKey
	 * @param int $limit
	 * @return int
	 */
	public function delByPrk($table, $primaryKey, $limit=1)
	{
		$sql = "DELETE FROM `$table` WHERE " . $this->primaryKeys[$table] . " = '" . $this->qstr($primaryKey) . "'";
		$limit = (int)$limit;
		if ($limit) { $sql .= " limit $limit"; }
		self::query($sql);
		return mysql_affected_rows();
	}//delByPrk
	
	//私有方法，获取表中字段的信息，返回多维数组
	protected function getTableFields($table)
	{
		if (!isset($this->tableFields[$table])) {
//			$table = $this->tablePrefix . $table;
			$table = $table;
			$sql = "DESCRIBE `$table`";
			$rows = self::findAllBySql($sql);
			$result=array();
			foreach ((array)$rows as $row) {
				$result[$row['Field']] = $row;
			}
			$this->tableFields[$table] = array_change_key_case($result);
		}
		return $this->tableFields[$table];
	}
	
	/*备份数据库，参数$tables如果手工设定地话，不需要给出表前缀
	$bakfile可以不指定（备份在PHP程序同一目录下），也可以是一个目录（自动生成个文件名备份在此目录下），也可以是一个包含路径的文件名
	*/
	public function backup($bakfile = null, $tables = array())
	{
		if (empty($bakfile)) {
			$bakfile = $this->dbname . date("Ymdhis") . '.sql';
		} elseif (is_dir($bakfile)) {
			if (preg_match('/\/$/', $bakfile)) {
				$bakfile = $bakfile . $this->dbname . date("Ymdhis") . '.sql';
			} else {
				$bakfile = $bakfile . '/' . $this->dbname . date("Ymdhis") . '.sql';
			}
		}
		if (!$tables) {
			$this->query("SHOW TABLES");
			while ($row = mysql_fetch_row($this->queryID)) {
				$tables[] = $row[0];
			}
		} else {
			foreach ($tables as $k => $v) {
				$tables[$k] = $this->tablePrefix . $v;
			}
		}
		
		if ($fp = fopen($bakfile, 'wb')) {
			if ($this->dbcharset) {
				fwrite($fp, "SET NAMES " . $this->dbcharset . ";\n\n");	
			}
			foreach ($tables as $table) {
				$this->dumpTable($table, $fp);
				fwrite($fp, "\n");
			}//foreach
			fclose($fp);
			return true;
		} else {
			return false;
		}//if
	}
	
	//私有方法 导出表格
	protected function dumpTable($table, $fp)
	{
		//备份表结构
		//fwrite($fp, "-- \n-- {$table}\n-- \n");
		$row = $this->findBySql("SHOW CREATE TABLE `{$table}`");
		fwrite($fp, str_replace("\n","", $row['Create Table']) . ";\n\n" );
		//备份表库数据
		$this->query("SELECT * FROM `{$table}`");
		while ($row = mysql_fetch_assoc($this->queryID)) {
			foreach ($row as $k=>$v) {
				$row[$k] = "'" . $this->qstr($v) . "'" ;	
			}
			$sql = "INSERT INTO `$table` VALUES (" . join(",", $row) . ");\n";
			fwrite($fp, $sql);
		}
		mysql_free_result($this->queryID);
		fwrite($fp, "\n");
	}
	
	//恢复数据库文件
	public function restore($bakfile)
	{
		if ($fp = fopen($bakfile, 'r')) {
			$sql = '';
			while (!feof($fp)) {
				$line = fgets($fp);
				if (strpos($line,'--')!==0)
				{
					$sql .= $line;
					//pp($sql);
				}
				if (preg_match('/;\s*$/', $sql)) {
					$this->query($sql);
					$sql = '';
				}
			}
			fclose($fp);
			return true;
		} else {
			return false;
		}
	}
	

	
}//class

?>