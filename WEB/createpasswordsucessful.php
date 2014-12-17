<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>create password sucessful</title>
</head>

<body>
<?php

  $conn = mysql_connect("localhost","root","123456")

         or die("Can not connect to database".mysql_error());

     mysql_select_db("ipv6")

         or die ("Can not connect to atca".mysql_error());

 if($_POST['password'] != $_POST['password1'])
	{ 
	 ?>
			 <script>
		window.alert('The password is not the same,Please input the right  Password!');
		</script>
		<script>location.href="createpassword.php";</script>
	 <?php }
		else 
		{
			 $username=$_POST['username'];$password=$_POST['password'];
			$sql = "Insert INTO passwd ( name,password) VALUES ( '". $username."','".$password."');";
			mysql_query($sql) or die("<br/>ERROR:<b>".mysql_error()."</b><br/>SQL:".$sql);
			mysql_close($conn);
			?>
			 <script>
		window.alert('Create Password sucessful,Please Login !');
		</script>
	
	 	<script>parent.location.href="login.php";</script>
		 <?php }
?>

</body>
</html>
