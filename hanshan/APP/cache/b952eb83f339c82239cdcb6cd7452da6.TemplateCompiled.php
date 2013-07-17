<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>
<style>
.t2{ border-collapse:collapse; border: 1px solid #06c; }
.t2 td{border:1px solid #cccccc; }
</style>
</head>

<body>
<?php include($this->template("header.html"));?>
<?php echo $time;?>

<?php echo 'hello';?>
<h1>User List</h1>
<p><a href="?r=.add">Add User</a></p>
<table class="t2">
  <tr>
    <td bgcolor="#CCCCCC"><strong>UID</strong></td>
    <td bgcolor="#CCCCCC"><strong>Username</strong></td>
    <td bgcolor="#CCCCCC"><strong>Password</strong></td>
    <td bgcolor="#CCCCCC"><strong>Email</strong></td>
    <td bgcolor="#CCCCCC"><strong>Created</strong></td>
    <td bgcolor="#CCCCCC"><strong>Modified</strong></td>
    <td bgcolor="#CCCCCC"><strong>Actions</strong></td>
  </tr>
  <tr>
<?php $__i=0; foreach ((array)$members as $m) { $__i++; ?>
    <td><a href="?r=.show&amp;uid=<?php echo $m['uid'];?>">"<?php echo $m['uid'];?>"
    </a>
    </td>
    <td><?php echo $m['username'];?></td>
    <td><?php echo $m['password'];?></td>
    <td><?php echo $m['email'];?></td>
    <td><?php echo date('Y-m-d H:i:s',$m['created']);?></td>
    <td><?php echo date('Y-m-d H:i:s',$m['modified']);?></td>
    <td><a href="?r=.edit&amp;uid=<?php echo $m['uid'];?>">Edit</a> <a href="?r=.delete&amp;uid=<?php echo $m['uid'];?>">Delete</a></td>
  </tr>
<?php } ?>
</table>
<div><?php echo $this->expendTime();?>(s)</div>
</body>
</html>
