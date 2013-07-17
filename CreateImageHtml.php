<?php
include('functions.php');

$path=trim($_GET['path']);
$path=r("\\",'/',$path);
$path=rtrim($path, '/');

if ($path) {
	preg_match('~/c\d+/?~i', $path, $ar);
//	pp($ar);
	$c = trim($ar[0], '/');
	$files = scandir($path);
	foreach ($files as $file) {
		if ('.jpg'==low(fileExt($file))) {
			if (preg_match('/^(\d+)_?(.+)(\.jpg)$/i', $file, $ar2)) {
				$id = (int)$ar2[1];
				$mainName = preg_replace('/(\d+)$/', "（\\1）", $ar2[2]);
				$youxu[$id] = array(
					"/img/$c/" . md5($file) . fileExt($file),
					$mainName,
				);
			} else {
				$mainName = preg_replace('/\.jpg$/i', '', $file);
				$mainName = preg_replace('/(\d+)$/', "（\\1）", $mainName);
				$wuxu[]=array(
					"/img/$c/" . md5($file) . fileExt($file),
					$mainName,
				);
			}
			
			file_exists($path."/$c") or mkdir($path."/$c");
			copy($path."/$file", $path."/$c/".md5($file).'.jpg');
		}
	}

	echo count($youxu).' ';
//	pp($youxu);
	echo count($wuxu);
//	pp($wuxu);
	
	$youxu_list="<ol>\n";
	$youxu_show = "<p></p>\n";
	foreach ((array)$youxu as $item) {
		$youxu_list.="<li><a href=\"{$item[0]}\" target=\"_blank\" >{$item[1]}</a></li>\n";
		$youxu_show.="<p><center><img src=\"{$item[0]}\"><br>{$item[1]}</center></p>\n";
	}
	$youxu_list.="</ol>";
	
	$wuxu_list="<ol>\n";
	$wuxu_show = "<p></p>\n";
	foreach ((array)$wuxu as $item) {
		$wuxu_list.="<li><a href=\"{$item[0]}\" target=\"_blank\" >{$item[1]}</a></li>\n";
		$wuxu_show.="<p><center><img src=\"{$item[0]}\"><br>{$item[1]}</center></p>\n";
	}
	$wuxu_list.="</ol>";
}
?>

<form id="form1" name="form1" method="get" action="">
  路径：
    <input name="path" type="text" id="path" size="60" value="<?php echo $path;?>" onmouseover="this.select()" />
  <input type="submit" name="button" id="button" value="提交" />
</form>
有序图片列表：<br>
  <textarea name="list" style="width=100%; height=200" id="list" onmouseover="this.select()"><?php echo $youxu_list; ?></textarea>
有序图片直接展示：<br>
  <textarea name="show" style="width=100%; height=200" id="show" onmouseover="this.select()"><?php echo $youxu_show; ?></textarea>
  
<hr style="color:red">

无序图片列表：<br>
  <textarea name="list" style="width=100%; height=200" id="list" onmouseover="this.select()"><?php echo $wuxu_list; ?></textarea>
无序图片直接展示：<br>
  <textarea name="show" style="width=100%; height=200" id="show" onmouseover="this.select()"><?php echo $wuxu_show; ?></textarea>