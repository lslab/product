<?php
for ($i=0; $i<20; $i++) {
echo mt_rand(0,11);
echo "\n";
	
}



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