<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
input{width:100%}
</style>
<script src="js/formset.js"></script>
</head>

<body>
<table border="0" align="center"><tr><td>
<form method="post">
<table width="500" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td width="100">论坛用户名</td>
    <td width="400"><input name="usr" type="text" id="usr" value="ggg" /></td>
    </tr>
  <tr>
    <td>论坛密码</td>
    <td><input name="pwd" type="text" id="pwd" value="ss" /></td>
    </tr>
  <tr>
    <td colspan="2"><hr width="500" style="height:1px" /></td>
    </tr>
</table>

<table width="500" cellpadding="3" cellspacing="0">
  <tbody>
    <tr>
      <td width="100">公司</td>
      <td width="400"><input type="text" name="company" id="company" /></td>
    </tr>
    <tr>
      <td>电话</td>
      <td><label>
        <input type="text" name="tel" id="tel" />
      </label></td>
    </tr>
    <tr>
      <td>手机</td>
      <td><input type="text" name="mtel" id="mtel" /></td>
    </tr>
    <tr>
      <td>在线报价</td>
      <td>
        <input type="text" name="price" id="price" style="width:50%" />
        （只需要填一个QQ号）
     </td>
    </tr>
    <tr>
      <td>地址</td>
      <td><input type="text" name="addr" id="addr" /></td>
    </tr>
  </tbody>
</table>
<table cellpadding="5" cellspacing="0">
  <tbody>
    <tr>
      <td>公司简介</td>
    </tr>
    <tr>
      <td>
<?php



$editor->Create();
?>
      
      
      </td>
    </tr>
    <tr>
      <td><div align="center">
        <input type="submit" name="button" id="button" value="确保保存" style="width:100px" />
      </div></td>
    </tr>
  </tbody>
</table>
</form>
</td></tr></table>

<?php echo $formset;?>


</body>
</html>
