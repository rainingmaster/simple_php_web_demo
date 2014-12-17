<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>create password sucessful</title>
</head>

<body>

<?php

$con = mysql_connect("localhost","root","123456");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("ipv6", $con);

mysql_query("UPDATE passwd SET password = '321'
WHERE name = 'test'");


			 $username=$_POST['username'];$password=$_POST['password'];

	              $sql="update passwd set password='".$password."'where name='".$username."'";

			mysql_query($sql)  or die("<br/>ERROR:<b>".mysql_error()."</b><br/>SQL:".$sql);
		 
                   	mysql_close($conn);
			?>
			 <script>window.alert('修改密码成功!');</script>
                      <script>parent.location.href="../user_main.php";</script>


</body>
</html>
