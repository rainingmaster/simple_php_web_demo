<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>static route</title>
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$ipv4_net = $_POST['ipv4_net'];
$ipv4_address = $_POST['ipv4_address'];

$check_result = 1;
$net = strstr($ipv4_net, '/',true); 
$length = substr(strstr($ipv4_net, '/'),1);
if($length<1 || $length>32)
	$check_result = 0;	

if(filter_var($net, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($ipv4_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $check_result==1)
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|ip route $ipv4_net $ipv4_address");
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|write");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="static_route.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

