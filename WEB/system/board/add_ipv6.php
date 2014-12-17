<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>add ipv6</title>
</head>
<body>
<?php
$ipv6_address  = $_POST['ipv6_address'];
$ipv6_board_eth = $_POST['ipv6_board_eth'];
$Board_IP = $_GET['Board_IP'];
$board_index = $_GET['board_index'];

	$check_result = 1;
	$net = strstr($ipv6_address, '/',true); 
	$length = substr(strstr($ipv6_address, '/'),1);
	if($length<1 || $length>128)
		$check_result = 0;	

	
if($check_result==1 && strpos($net,":"))
{
snmp_set_quick_print(1);
$result1 = snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|interface $ipv6_board_eth");
$result2 = snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","3|ipv6 address $ipv6_address");
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
