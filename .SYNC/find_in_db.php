<pre>
<?php

$key = 'xiaohd8';

$db = new DB('localhost', 'root', '', 'discuz');
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
            //if (strpos(strtolower($cell), strtolower($key))!==false)	//ģ������
            if (strtolower($cell)==strtolower($key))					//��ȷ����
            {
				echo "<h3>$table</h3>";
				print_r($row);
				echo '<hr>';
            }
        }
    }
}

/////////////////////////////////////////


class DB {

	public $debug;	//����
	public $primaryKeys = array();	//�趨������������ڴ�����ʵ��������趨������ $db->primaryKeys(array('��1'=>'�����ֶ�1','��2'=>'�����ֶ�2'))
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
		
		//Сд
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
	 * ��ѯMYSQL�汾����ȷ���Ƿ�Ҫ��set names xxx
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
	 * ����sql���
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
	 * ����select���
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
	 * ����insert into ���
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
	 * ���� update ���
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
	 * ����delete���
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
	
	//ת������ַ�
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
	
	//˽�з�������ʽ���ֶΣ��� field ��Ϊ `table`.`field`
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
	 * �ϴβ�������ʱ�Զ�ID
	 *
	 * @return int
	 */
	public function insertId()
	{
		return mysql_insert_id();
	}//liastID
	
	/**
	 * ����������ת��Ϊ����ֱ��ʹ�õ�����
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
	 * ���ҵ�������
	 *
	 * @param string $table ����
	 * @param array/sring $fields ��ѯ�ֶ�
	 * @param string/int/array $conditions ��ѯ��������Ϊ��ֵʱ�����ǲ�ѯ������ֵ
	 * @param string $sort ���򷽷�
	 * @return array
	 */
	public function find ($table, $fields= '*', $conditions=null, $sort=null) 
	{
		$sql = self::buildSelectSQL($table, $fields, $conditions, $sort, 1);
		return self::findBySql($sql);
	}
	
	/**
	 * ����������ѯ
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
	 * ����ĳ���ֶε�ֵ
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
	 * ��ѯ��������
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string/int/array $conditions
	 * @param string $sort
	 * @param int $limit ���Ʋ�ѯ��������
	 * @param int $page ��ҳ��ѯʱ�趨��Nҳ���ֲ���ѯ��ʹ��findPage()
	 * @return ��ά����
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
	 * ��ҳ����
	 *
	 * @param string $table
	 * @param string $fields
	 * @param string/array $conditions
	 * @param string $sort
	 * @param int $page ��Nҳ
	 * @param int $pagesize ÿҳ���ص�������
	 * @return ��ά����
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
	 * ���ط���������������
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
	 * ֱ��ʹ��SQL����ѯ�����ص������ݣ������Զ������ǰ׺���ַ�ת��ȹ���
	 *
	 * @param string $sql
	 * @return ����һά����
	 */
	public function findBySql ($sql) 
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
	 * ���������룩һ�����ݣ����Զ���������$row�еļ�ֵ�����˱��ֶ��в����ڵļ�ֵ
	 *
	 * @param string $table
	 * @param array $row &#65533;��ά����
	 * @return ���ݱ���&#65533;���ز��������ֵ
	 */
	public function create($table, $row)
	{
		self::query($this->buildInsertSQL($table,$row));
		return mysql_affected_rows();
	}
	
	/**
	 * ���µ������ݣ����Զ���������$row�еļ�ֵ�����˱��ֶ��в����ڵļ�ֵ
	 *
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return �������ݱ���Ӱ�������
	 */
	public function update($table,$row,$conditions=null)
	{
		self::query(self::buildUpdateSQL($table, $row, $conditions));
		return mysql_affected_rows();
	}
	
	/**
	 * ����$row�������Ƿ��б�����ֵ��������������ݣ�
	 * $row�к��б�����ִֵ��update()��������ִ֮��create()��������create()��update()�Ľ����
	 * ���Զ���������$row�еļ�ֵ�����˱��ֶ��в����ڵļ�ֵ
	 * @param string $table
	 * @param array $row
	 * @param string $conditions
	 * @return �����Ǵ������Ǹ��·������ݱ���������ֵ����Ӱ�������
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
	 * ɾ��������������
	 *
	 * @param string $table
	 * @param string $conditions
	 * @param int $limit ����ɾ�����ݵ�����
	 * @return ����ɾ������Ӱ�������
	 */
	public function delete($table, $conditions, $limit = null)
	{
		self::query($this->buildDeleteSQL($table, $conditions, $limit));
		return mysql_affected_rows();
	}
	
	/**
	 * ��������ɾ��
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
	
	//˽�з�������ȡ�����ֶε���Ϣ�����ض�ά����
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
	
	/*�������ݿ⣬����$tables����ֹ��趨�ػ�������Ҫ������ǰ׺
	$bakfile���Բ�ָ����������PHP����ͬһĿ¼�£���Ҳ������һ��Ŀ¼���Զ����ɸ��ļ��������ڴ�Ŀ¼�£���Ҳ������һ������·�����ļ���
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
	
	//˽�з��� �������
	protected function dumpTable($table, $fp)
	{
		//���ݱ�ṹ
		//fwrite($fp, "-- \n-- {$table}\n-- \n");
		$row = $this->findBySql("SHOW CREATE TABLE `{$table}`");
		fwrite($fp, str_replace("\n","", $row['Create Table']) . ";\n\n" );
		//���ݱ������
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
	
	//�ָ����ݿ��ļ�
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

