<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
$Board_IP =$_GET['Board_IP'];
$IPv6_address =$_POST['IPv6_address'];
$IPv6_address_prefixlen =$_POST['IPv6_address_prefixlen'];

$check_result = 1;
if($IPv6_address_prefixlen>1 || $IPv6_address_prefixlen>128)
	$check_result = 0;

if(filter_var($IPv6_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) && strpos($IPv6_address,":") && $check_result==1)
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.1.3.0","s","$IPv6_address");
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.1.4.0","i","$IPv6_address_prefixlen");
}
else
{
	?>
	<script>window.alert('IPv6地址输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

