<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
$Board_IP =$_GET['Board_IP'];
$IPv6Prefix =$_POST['IPv6Prefix'];

if(strpos($IPv6Prefix,":"))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54323.1.3.0","s","$IPv6Prefix");
}
else
{
?>
<script>window.alert('转换前缀输入有误，请重新配置！');</script>
<?php
}
?>
<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

