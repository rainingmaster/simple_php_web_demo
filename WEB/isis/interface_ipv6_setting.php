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
$ipv6_address = $_POST['ipv6_address'];
$Board_IP = $_GET['Board_IP'];
$result1 = 0;
$result2 = 0;
$check_result = 1;
$net = strstr($ipv6_address, '/',true); 
$length = substr(strstr($ipv6_address, '/'),1);
if($length<1 && $length>128)
	$check_result = 0;	

snmp_set_quick_print(1);
$result1 = snmpset($Board_IP,"public",".1.3.6.1.4.1.400.1.1.0","s","2|interface $interface");

if($result1 == 1)
{
	if(strpos($net,":") && $check_result==1)
	{
		$result2 = snmpset($Board_IP,"public",".1.3.6.1.4.1.400.1.1.0","s","3|ipv6 address $ipv6_address");
		if($result2 == 1)
		{
			$ini->iniWrite("isis--".$Board_IP, "interface", $interface);
			$ini->iniWrite("isis--".$Board_IP, "ipv6", $ipv6_address);
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
<?php 
}
?>
<script>location.href="isis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>