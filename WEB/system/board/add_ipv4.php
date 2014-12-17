<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>add ipv4</title>
</head>
<body>
<?php
$ipv4_address  = $_POST['ipv4_address'];
$board_eth = $_POST['board_eth'];
$Board_IP = $_GET['Board_IP'];
$board_index = $_GET['board_index'];

	$check_result = 1;
	$net = strstr($ipv4_address, '/',true); 
	$length = substr(strstr($ipv4_address, '/'),1);
	if($length<1 || $length>32)
		$check_result = 0;	

	
if($check_result==1 && filter_var($net, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
{
snmp_set_quick_print(1);
$result1 = snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|interface $board_eth");
$result2 = snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","3|ip address $ipv4_address");
$result3 = snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|write");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="board_ip.php?Board_IP=<?php echo $Board_IP; ?>&board_index=<?php echo $board_index; ?>"</script>
</body>
