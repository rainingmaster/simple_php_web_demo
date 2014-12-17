<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$IPv4_dns =$_POST['IPv4_dns'];
if(filter_var($IPv4_dns, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.1.5.0","a","$IPv4_dns");
}
else
{
	?>
	<script>window.alert('IPv4 DNS输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

