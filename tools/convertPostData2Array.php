<form name="FormName" action="" method="post">
<textarea name="str" style="width:500px; height:200px;"></textarea>
<input type="submit" value="Send" style="width:100px">
</form>

<?php
if (!empty($_POST['str']))
{
	$str = stripslashes($_POST['str']);
	$str = str_replace('&', "',\n'", $str);
	$str = str_replace('=', "'\t=>\t'", $str);
	$str = str_replace('&', "',\n'", $str);
	$str = "'{$str}'";
	$str = urldecode($str);
	echo '<pre>';
	echo $str;
	echo '</pre>';
}

?>