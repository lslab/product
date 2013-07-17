<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>
<script src="images/formset.js"></script>
<style>
.t2{ border-collapse:collapse; border: 1px solid #06c; }
.t2 td{border:1px solid #cccccc; }
</style>
</head>

<body>
<?php include($this->template("header.html"));?>

<h1>Add User</h1>
<form method="post">
<table width="500" border="0" cellpadding="5" cellspacing="0" class="t2">
  <tr>
    <td>Username    </td>
    <td><input type="text" name="username" id="username" style="width:300px;" /></td>
  </tr>
  <tr>
    <td>Password    </td>
    <td><input type="text" name="password" id="password" style="width:300px" /></td>
  </tr>
  <tr>
    <td>Email</td>
    <td><input type="text" name="email" id="email" style="width:300px" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="提交" /></td>
  </tr>
</table>
</form>

<div><?php echo $this->expendTime();?>(s)</div>

</body>
</html>
