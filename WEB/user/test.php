<?php
$con = mysql_connect("localhost","root","123456");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("ipv6", $con);

mysql_query("UPDATE passwd SET password = '12345678'
WHERE name = 'test'");

mysql_close($con);
?>
