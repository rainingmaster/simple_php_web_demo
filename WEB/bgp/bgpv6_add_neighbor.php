<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>bgpv6</title>
</head>
<body>
<?php		
$bgpv6_neighbor = $_POST['bgpv6_neighbor'];
$bgpv6_neighborid = $_POST['bgpv6_neighborid'];
$Board_IP =$_GET['Board_IP'];
$bgp_id =$_GET['bgp_id'];
if(is_numeric($bgpv6_neighborid) && strpos($bgpv6_neighbor,":"))
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|router bgp $bgp_id");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","2|neighbor $bgpv6_neighbor remote-as $bgpv6_neighborid");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","2|address-family ipv6");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","7|neighbor $bgpv6_neighbor activate");
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

