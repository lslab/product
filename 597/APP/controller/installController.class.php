<?php

class installController extends Controller {

	public function indexAction()
	{
		if (!isset($_GET['ok'])) {
			echo '<a href="?r=install&ok">Click here to INSTALL</a>';
		} else {
			$sqls=explode("\n",file_get_contents(APP_PATH.'/config/db.sql'));
			$sql='';
			foreach ($sqls as $line) {
				$sql.=$line;
				if (preg_match('/;$/',trim($line))) {
					$sql=str_replace('{TABLE_PREFIX}',App::config('db_table_prefix'),$sql);
					$this->db()->query($sql);
					$sql='';
				}
			}

			echo 'install success <A HREF="index.php">Go to HOME</A>';
		}
	}
}



?>