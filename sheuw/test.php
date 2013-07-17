<?php

for ($i=0; $i<=30; $i++) {
	$d=sprintf("%02d",$i);
	$r[]='copy E:\img\banwan\2013\03'.$d."\*.* e:\sheuw";
}

echo join("\r\n",$r);