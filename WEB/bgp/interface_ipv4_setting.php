<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
include('../include/ssh2.php');
$ini = new ini();	
$interface = $_POST['interface'];
$ipv4_address = $_POST['ipv4_address'];
$Board_IP = $_GET['Board_IP'];
$result1 = 0;
$result2 = 0;
$check_result = 1;
$net = strstr($ipv4_address, '/',true); 
$length = substr(strstr($ipv4_address, '/'),1);
if($length<1 && $length>32)
	$check_result = 0;	

snmp_set_quick_print(1);
$result1 = snmpset($Board_IP,"public",".1.3.6.1.4.1.400.1.1.0","s","2|interface $interface");

if($result1 == 1)
{
	if(filter_var($net, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $check_result==1)
	{
		$result2 = snmpset($Board_IP,"public",".1.3.6.1.4.1.400.1.1.0","s","3|ip address $ipv4_address");
		if($result2 == 1)
		{
			$ini->iniWrite("bgp--".$Board_IP, "interface", $interface);
			$ini->iniWrite("bgp--".$Board_IP, "ipv4", $ipv4_address);
			$ini->iniUpdate();
		}
		else
		{?>
			<script>window.alert('IP地址无法设置!');</script>
		<?php }
	}
	else
	{?>
		<script>window.alert('IP地址输入有误!');</script>
	<?php }

}
else
{?>
<script>window.alert('该网卡不能设置!');</script>
<?php }

?>
<script>location.href="bgp_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>