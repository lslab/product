<?php
//GBK����
ini_set('include_path', dirname(__FILE__) . '/PEAR');
include_once('Spreadsheet/Excel/Writer.php');

$lines=file("./0.txt");
foreach ($lines as $line) {
	$tmp=explode("\t",$line);
	$r[trim($tmp[2])][]=$tmp;
}

foreach ($r as $bj=>$stu) {
	$nj=13 - substr($bj,0,2);
	$class = $nj . substr($bj,3,1);

	$workbook = new Spreadsheet_Excel_Writer_Workbook(dirname(__FILE__) . "/$class.xls");
	$worksheet = $workbook->addWorkSheet('ѧ������');
	$worksheet->writeRow(0, 0, array('���','����','�Ա�','��ע'));

	$n=1;
	foreach ($stu as $ar) {
		$worksheet->writeRow($n,0,array($n,$ar[0],$ar[1]));
		$n++;
	}
	
	$workbook->close();

}

echo 'ok';