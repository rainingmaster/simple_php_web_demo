<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$IPv6_dns_prefix =$_POST['IPv6_dns_prefix'];
$IPv6_dns_prefix1 = str_replace("FF", "ff", $IPv6_dns_prefix);

if(strpos($IPv6_dns_prefix,":"))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.1.6.0","s","$IPv6_dns_prefix1");
}
else
{
	?>
	<script>window.alert('IPv6 DNS输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

