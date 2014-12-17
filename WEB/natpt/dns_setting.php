<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php		
$Board_IP =$_GET['Board_IP'];
$DNS_address =$_POST['DNS_address'];
if(filter_var($DNS_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54323.1.4.0","a","$DNS_address");
}
else
{
?>
<script>window.alert('DNS输入有误，请重新配置！');</script>
<?php
}
?>
<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

