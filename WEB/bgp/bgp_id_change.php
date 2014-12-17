<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>bgpv4</title>
</head>
<body>
<?php	
$old_bgp_id =$_GET['bgp_id'];	
$Board_IP =$_GET['Board_IP'];	
$new_bgp_id = $_POST['new_bgp_id'];

if(is_numeric($new_bgp_id))
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|no router bgp $old_bgp_id");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|router bgp $new_bgp_id");
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

