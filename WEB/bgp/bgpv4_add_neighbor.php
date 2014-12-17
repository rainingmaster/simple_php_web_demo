<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>bgpv4</title>
</head>
<body>
<?php	
$bgp_id =$_GET['bgp_id'];	
$Board_IP =$_GET['Board_IP'];	
$bgpv4_neighbor = $_POST['bgpv4_neighbor'];
$bgpv4_neighborid = $_POST['bgpv4_neighborid'];
$check_result = 1;
if(!filter_var($bgpv4_neighbor, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
	$check_result = 0;	
if(!is_numeric($bgpv4_neighborid))
	$check_result = 0;

if($check_result == 1)
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|router bgp $bgp_id");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","2| neighbor $bgpv4_neighbor remote-as $bgpv4_neighborid");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","2|address-family  ipv4 multicast");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","5|neighbor $bgpv4_neighbor activate");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|write memory");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="bgp_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

