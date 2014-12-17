<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php

$Board_IP =$_GET['Board_IP'];	
$IPv6_address =$_POST['IPv6_address'];

if(strpos($IPv6_address,":"))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54322.1.2.0","s","$IPv6_address");
}
else
{
	?>
	<script>window.alert('IPv6地址输入有误，请重新配置！');</script>
	<?php
}


?>
<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

