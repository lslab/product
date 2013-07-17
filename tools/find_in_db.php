<?php

$searchkey = '871';
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$dbname='aliqq';
$dbcharset='gbk';

################### ���²��ֲ�Ҫ�޸� ####################

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

	var $tablePrefix;	//��ǰ׺
	var $debug;	//����
	var $primaryKeys = array();	//�趨������������ڴ�����ʵ��������趨������ $db->primaryKeys(array('��1'=>'�����ֶ�1','��2'=>'�����ֶ�2'))
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

	//˽�з�����һ�㲻Ҫ������
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
	
	//˽�з���
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
	
	//˽�з���
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
	
	//˽�з���
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
	
	//˽�з���
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
	
	//ת������ַ�
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
	
	//˽�з�������ʽ���ֶΣ��� field ��Ϊ `table`.`field`
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
	 * ���ҵ�������
	 *
	 * @param string $table ����
	 * @param array/sring $fields ��ѯ�ֶ�
	 * @param string/int $conditions ��ѯ��������Ϊ��ֵʱ�����ǲ�ѯ������&#65533;? 
	 * @param string $sort ���򷽷�
	 * @return array
	 */
	function find ($table, $fields= '*', $conditions=null, $sort=null) 
	{
		$sql = $this->buildSelectSQL($table, $fields, $conditions, $sort, 1);
		return $this->findBySql($sql);
	}
	
	/**
	 * ��ѯ��������
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string/int $conditions
	 * @param string $sort
	 * @param int $limit ���Ʋ�ѯ��������
	 * @param int $page ��ҳ��ѯʱ�趨��Nҳ���ֲ���ѯ��ʹ��findPage()
	 * @return ��ά����
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
	 * ��ҳ����
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string $conditions
	 * @param string $sort
	 * @param int $page ��Nҳ
	 * @param int $pagesize ÿҳ���ص�������
	 * @return ��ά����
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
	 * ���ط���������������
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
	 * ֱ��ʹ��SQL����ѯ�����ص������ݣ������Զ������ǰ׺���ַ�ת��ȹ���
	 *
	 * @param string $sql
	 * @return ����һά����
	 */
	function findBySql ($sql) 
	{
		$r = mysql_fetch_assoc($this->query($sql));
		mysql_free_result($this->queryID);
		return $r;
	}
	
	/**
	 * ֱ��ʹ��SQL����ѯ�����ض������ݣ������Զ������ǰ׺���ַ�ת��ȹ���
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
	 * ���������룩һ�����ݣ����Զ���������$row�еļ�ֵ�����˱��ֶ��в����ڵļ�ֵ
	 *
	 * @param string $table
	 * @param array $row &#65533;?ά��&#65533;?
	 * @return ���ݱ���&#65533;?����������&#65533;? 
	 */
	function create($table, $row)
	{
		$this->query($this->buildInsertSQL($table,$row));
		return mysql_insert_id();
	}
	
	/**
	 * ���µ������ݣ����Զ���������$row�еļ�ֵ�����˱��ֶ��в����ڵļ�ֵ
	 *
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return �������ݱ���Ӱ�������
	 */
	function update($table,$row,$conditions=null)
	{
		$this->query($this->buildUpdateSQL($table, $row, $conditions));
		return mysql_affected_rows();
	}
	
	/**
	 * ����$row�������Ƿ��б�����ֵ��������������ݣ�$row�к��б�����ִֵ��update()��������ִ֮��create()��������create()��update()�Ľ����
	 * ���Զ���������$row�еļ�ֵ�����˱��ֶ��в����ڵļ�ֵ
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return �����Ǵ������Ǹ��·������ݱ���������ֵ����Ӱ�������
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
	 * ɾ��������������
	 *
	 * @param string $table
	 * @param string $conditions
	 * @param int $limit ����ɾ�����ݵ�����
	 * @return ����ɾ������Ӱ�������
	 */
	function delete($table, $conditions, $limit = null)
	{
		$this->query($this->buildDeleteSQL($table, $conditions, $limit));
		return mysql_affected_rows();
	}
	
	//˽�з�������ȡ�����ֶε���Ϣ�����ض�ά����
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
	
	/*�������ݿ⣬����$tables����ֹ��趨�ػ�������Ҫ������ǰ׺
	$bakfile���Բ�ָ����������PHP����ͬһĿ¼�£���Ҳ������һ��Ŀ¼���Զ����ɸ��ļ��������ڴ�Ŀ¼�£���Ҳ������һ������·�����ļ���
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
	
	//˽�з��� �������
	function dumpTable($fullTableName, $fp)
	{
		//���ݱ�ṹ
		//fwrite($fp, "-- \n-- {$fullTableName}\n-- \n");
		$row = $this->findBySql("SHOW CREATE TABLE `{$fullTableName}`");
		fwrite($fp, str_replace("\n","", $row['Create Table']) . ";\n\n" );
		//���ݱ������
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
	
	//�ָ����ݿ��ļ�
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