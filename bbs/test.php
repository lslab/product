<?php

$s='1203';
echo substr($s,3,1);



die;///////////////////
	$pinyins=array (
	'美食' => 'meishi',
	'咖啡厅' => 'kafeiting',
	'酒吧' => 'jiuba',
	'茶馆' => 'chaguan',
	'宾馆酒店' => 'binguanjiudian',
	'洗浴' => 'xiyu',
	'足疗' => 'zuliao',
	'按摩' => 'anmo',
	'KTV' => 'ktv',
	'夜总会' => 'yezonghui',
	'娱乐城' => 'yulecheng',
	'舞厅' => 'wuting',
	);
	
	foreach ($pinyins as $d) {
		mkdir("e:/img/$d",777,true);
		mkdir("e:/comment/$d",777,true);
	}