<?php

class mainController extends baseController {

	public function indexAction()
	{

		$xls = new Spreadsheet_Excel_Reader();
		$xls->setOutputEncoding('UTF-8');
		$xls->read(APP_PATH.'/../cs.xls');


		for ($i=2; $i<=8000; $i++) {
			$a=$xls->sheets[0]['cells'][$i][1];
			$this->tbl->create(array('name'=>$a,'p'=>mt_rand(1,200)));
		}
		echo 'ok';
	}


}

