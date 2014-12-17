<?php
 
	 function getIP()

{

global $ip;

if (getenv("HTTP_CLIENT_IP"))

$ip = getenv("HTTP_CLIENT_IP");

else if(getenv("HTTP_X_FORWARDED_FOR"))

$ip = getenv("HTTP_X_FORWARDED_FOR");

else if(getenv("REMOTE_ADDR"))

$ip = getenv("REMOTE_ADDR");

else $ip = "Unknow";

return $ip;
}


     session_start();
     $conn = mysql_connect("localhost","root","123456")

         or die("Can not connect to database".mysql_error());
      mysql_query("use ipv6;");

$username=$_POST['username'];
$password=$_POST['password'];
$sql = "select * from passwd where name='$username' and password='$password'";
$Result = mysql_query($sql, $conn);
$onerow = mysql_fetch_array($Result);
 
if (  $onerow )
       {
	 ?>
           <script>location.href="mainpage.php";</script>
	 <?php }
	 else
	 {
	 ?>
                <script>window.alert('User Name or Password wrong!!');
                </script>
		<script>location.href="login.php";</script>
	 <?php }
     mysql_close($conn);

  ?>

