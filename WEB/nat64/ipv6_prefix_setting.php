<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];
$IPv6_prefix =$_POST['IPv6_prefix'];
$IPv6_prefix_len =$_POST['IPv6_prefix_len'];
$valid_prefix_length = array("96", "64", "56", "48","40","32");

if(strpos($IPv6_prefix,":") && in_array($IPv6_prefix_len, $valid_prefix_length))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54322.2.1.2.0","s","$IPv6_prefix_process");
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54322.2.1.3.0","i","$IPv6_prefix_len");
}
else
{
	?>
	<script>window.alert('IPv6前缀输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

