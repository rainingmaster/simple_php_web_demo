<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>rip network delete</title>
</head>
<body>
<?php

$ripv4_net =$_POST['ripv4_net'];
$Board_IP =$_GET['Board_IP'];
$check_result = 1;

	$net4 = strstr($ripv4_net, '/',true); 
	$length4 = substr(strstr($ripv4_net, '/'),1);
	if(!filter_var($net4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$check_result = 0;	

	
if($check_result==1)
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.23.6.2.0","s","1|router rip");
snmpset($Board_IP,"public",".1.3.6.1.2.1.23.6.2.0","s","2|network $ripv4_net");
snmpset($Board_IP,"public",".1.3.6.1.2.1.23.6.2.0","s","1|write memory");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="rip_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>
