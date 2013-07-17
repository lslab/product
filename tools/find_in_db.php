<?php

$searchkey = '871';
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$dbname='aliqq';
$dbcharset='gbk';

################### 以下部分不要修改 ####################

$db = new DB($dbhost,$dbuser,$dbpass,$dbname,$dbcharset);
$db->query('show tables');
while ($row = mysql_fetch_array($db->queryID))
{
	$tables[] = $row[0];
}

foreach ($tables as $table)
{
	$rs = $db->findAll($table);
    foreach ((array)$rs as $row)
    {
        foreach ($row as $cell)
        {
            if (strpos(strtolower($cell), strtolower($searchkey))!==false)
            {
				echo "<h3>$table</h3>";
				print_r($row);
				echo '<hr>';
            }
        }
    }
}

///////////////////
class DB {

	var $tablePrefix;	//表前缀
	var $debug;	//调试
	var $primaryKeys = array();	//设定各表的主键，在创建类实例后必须设定表主键 $db->primaryKeys(array('表1'=>'主键字段1','表2'=>'主键字段2'))
	var $queryCount = 0;
	var $haltOnError = true;
	var $displayError=true;
		
	var $queryID;
	var $connected = false;
	var $tableFields = array();
	var $dbhost;
	var $dbuser;
	var $dbpwd;
	var $dbname;
	var $dbcharset;
	var $pconnect = false;

	function DB($host, $user, $pwd, $database = null, $charset = null, $pconnect = null) {
		$this->dbhost = $host;
		$this->dbuser = $user;
		$this->dbpwd  = $pwd;
		$this->dbname = $database;
		$this->dbcharset = $charset;
		$this->pconnect = $pconnect;
	}
	
	function connect()
	{
		if ($this->pconnect) {
			@mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpwd) || die('<b>MySQL ERROR:</b> ' . mysql_error());
		} else {
			@mysql_connect($this->dbhost, $this->dbuser, $this->dbpwd) || die('<b>MySQL ERROR:</b> ' . mysql_error());
		}
		if ($this->dbname != null) {
			$this->selectdb($this->dbname);
		}
	}

	function selectdb($dbname) {
		$this->dbname = $dbname;
		mysql_select_db($this->dbname) || die('<b>MySQL ERROR(mysql_selectt_db):</b> ' . mysql_error());
		if ($this->dbcharset) {
			mysql_query("SET NAMES " . $this->dbcharset) || die('<b>MySQL ERROR:</b> ' . mysql_error());
		}
	}

	//私有方法，一般不要调用它
	function query($sql)
	{
		if ($this->debug) {
			echo "<p><b>MySQL DEBUG:</b> $sql</p>";
		}
		if (!$this->connected) {
			$this->connect();
			$this->connected = true;
		}
		$this->queryID = @mysql_query($sql);
		if (!$this->queryID) {
			if ($this->displayError) {
				print('<p><b>MySQL DEBUG:</b> ' . $sql . '</p>');
				echo '<p><b>MySQL ERROR:(' . mysql_errno() . ')</b> ' . mysql_error() . '</p>';
			}
			$this->haltOnError && die("\n mySQL error and session halted");
		}
		$this->queryCount++;
		return $this->queryID;
	}
	
	//私有方法
	function buildSelectSQL($table, $fields, $conditions, $sort, $limit)
	{
		$fullTableName = $this->tablePrefix . $table;
		$sql = "SELECT " . $this->qfield($fullTableName, $fields) . " FROM `$fullTableName`";
		if (!empty($conditions)) {
			if (is_int($conditions) || (int)$conditions) {
				$sql .= ' WHERE ' . $this->primaryKeys[$table] . " = '" . $this->qstr($conditions) . "'";
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
	
	//私有方法
	function buildInsertSQL($table, $row)
	{
		$fullTableName = $this->tablePrefix . $table;
		$fields = $this->getTableFields($table);
		
		foreach ($row as $k=>$v) {
			if (isset($fields[$k])) {
				$sqlrow[$k] = "'" . $this->qstr($v) . "'" ;	
			}
		}
		$sql = "INSERT INTO `$fullTableName` (" . $this->qfield($fullTableName, array_keys($sqlrow)) . ") VALUES (".
			join(",", $sqlrow) . ");\n";
		return $sql;
	}
	
	//私有方法
	function buildUpdateSQL($table, $row, $condition)
	{
		$fullTableName = $this->tablePrefix . $table;
		$fields = $this->getTableFields($table);
		
		$sql = "UPDATE `$fullTableName` SET ";
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
		if ($condition) {
			$sql .= " AND $condition";
		}
		return $sql;
	}
	
	//私有方法
	function buildDeleteSQL($table, $conditions, $limit = null)
	{
		$fullTableName = $this->tablePrefix . $table;
		$sql = "DELETE FROM $fullTableName";
		if ($conditions) {
			if (is_int($conditions) || (int)$conditions) {
				$sql .= " WHERE " . $this->primaryKeys[$table] . " = '" . $this->qstr($conditions) . "'";
			} else {
				$sql .= " WHERE $conditions";
			}
		}//if $conditions
		if ($limit) {
			$sql .= " LIMIT " . (int)$limit;
		}
		return $sql;
	}
	
	//转义相关字符
	function qstr($str, $addComma = false)
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
	function qfield($table, $fields)
	{
		$table = "`$table`.";
		!$fields && $fields = '*';
		if ('*' != $fields) {
			if (!is_array($fields)) {
				$fields = explode(',', $fields);
			}
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
		return join(',', $result);
	}
	
	/**
	 * 查找单条数据
	 *
	 * @param string $table 表名
	 * @param array/sring $fields 查询字段
	 * @param string/int $conditions 查询条件。当为数值时，就是查询表主键&#65533;? 
	 * @param string $sort 排序方法
	 * @return array
	 */
	function find ($table, $fields= '*', $conditions=null, $sort=null) 
	{
		$sql = $this->buildSelectSQL($table, $fields, $conditions, $sort, 1);
		return $this->findBySql($sql);
	}
	
	/**
	 * 查询多条数据
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string/int $conditions
	 * @param string $sort
	 * @param int $limit 限制查询数据条数
	 * @param int $page 分页查询时设定第N页，分布查询请使用findPage()
	 * @return 多维数组
	 */
	function findAll ($table, $fields= '*', $conditions=null, $sort=null, $limit=null, $page=null) 
	{
		if ($limit) {
			$limit = (int)$limit;
		}
		if ($limit && $page) {
			$limit = (int)$limit * (int)$page . ',' . (int)$limit;
		}
		$sql = $this->buildSelectSQL($table, $fields, $conditions, $sort, $limit);
		return $this->findAllBySql($sql);
	}
	
	/**
	 * 分页查找
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string $conditions
	 * @param string $sort
	 * @param int $page 第N页
	 * @param int $pagesize 每页返回的数量数
	 * @return 多维数组
	 */
	function findPage ($table, $fields= '*', $conditions=null, $sort=null, $page, $pagesize) 
	{
		(int)$page < 1 && $page=1;
		$limit = (int)$pagesize * ((int)$page - 1) . ',' . (int)$pagesize;
		$sql = $this->buildSelectSQL($table, $fields, $conditions, $sort, $limit);
		$sqlpage = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) as c FROM ', 
			$this->buildSelectSQL($table, $fields, $conditions, $sort, null));
		$c = $this->findBySql($sqlpage);
		$result['count'] = $c['c'];	
		$result['rowset'] = $this->findAllBySql($sql);
		$result['pagecount'] = ceil($result['count'] / $pagesize);
		return $result;
	}
	
	/**
	 * 返回符合条件的数据数
	 *
	 * @param string $table
	 * @param string $conditions
	 * @return int
	 */
	function findCount($table, $conditions)
	{
		$fullTableName = $this->tablePrefix . $table;
		$row = $this->findBySql("SELECT COUNT(*) AS c FROM $fullTableName WHERE $conditions");
		return $row['c'];
	}
	
	/**
	 * 直接使用SQL语句查询，返回单条数据，不会自动处理表前缀、字符转义等工作
	 *
	 * @param string $sql
	 * @return 返回一维数组
	 */
	function findBySql ($sql) 
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
	function findAllBySql ($sql) 
	{
		$this->query($sql);
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
	 * @param array $row &#65533;?维数&#65533;?
	 * @return 数据表中&#65533;?后插入的主键&#65533;? 
	 */
	function create($table, $row)
	{
		$this->query($this->buildInsertSQL($table,$row));
		return mysql_insert_id();
	}
	
	/**
	 * 更新单条数据，会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 *
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return 返回数据表受影响的行数
	 */
	function update($table,$row,$conditions=null)
	{
		$this->query($this->buildUpdateSQL($table, $row, $conditions));
		return mysql_affected_rows();
	}
	
	/**
	 * 根据$row数组中是否含有表主键值来创建或更新数据，$row中含有表主键值执行update()操作，反之执行create()操作，是create()与update()的结合体
	 * 会自动分析数组$row中的键值，过滤表字段中不存在的键值
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return 根据是创建还是更新返回数据表插入的主键值或受影响的行数
	 */
	function save($table, $row, $conditions=null)
	{
		if (isset($row[$this->primaryKeys[$table]])) {
			return $this->update($table, $row, $conditions);
		} else {
			return $this->create($table, $row);
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
	function delete($table, $conditions, $limit = null)
	{
		$this->query($this->buildDeleteSQL($table, $conditions, $limit));
		return mysql_affected_rows();
	}
	
	//私有方法，获取表中字段的信息，返回多维数组
	function getTableFields($table)
	{
		if (!isset($this->tableFields[$table])) {
			$fullTableName = $this->tablePrefix . $table;
			$sql = "DESCRIBE $fullTableName";
			$rows = $this->findAllBySql($sql);
			foreach ((array)$rows as $row) {
				$result[$row['Field']] = $row;
			}
			$this->tableFields[$table] = $result;
		}
		return $this->tableFields[$table];
	}
	
	/*备份数据库，参数$tables如果手工设定地话，不需要给出表前缀
	$bakfile可以不指定（备份在PHP程序同一目录下），也可以是一个目录（自动生成个文件名备份在此目录下），也可以是一个包含路径的文件名
	*/
	function backup($bakfile = null, $tables = array())
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
	function dumpTable($fullTableName, $fp)
	{
		//备份表结构
		//fwrite($fp, "-- \n-- {$fullTableName}\n-- \n");
		$row = $this->findBySql("SHOW CREATE TABLE `{$fullTableName}`");
		fwrite($fp, str_replace("\n","", $row['Create Table']) . ";\n\n" );
		//备份表库数据
		$this->query("SELECT * FROM `{$fullTableName}`");
		while ($row = mysql_fetch_assoc($this->queryID)) {
			foreach ($row as $k=>$v) {
				$row[$k] = "'" . $this->qstr($v) . "'" ;	
			}
			$sql = "INSERT INTO `$fullTableName` VALUES (" . join(",", $row) . ");\n";
			fwrite($fp, $sql);
		}
		mysql_free_result($this->queryID);
		fwrite($fp, "\n");
	}
	
	//恢复数据库文件
	function restore($bakfile)
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