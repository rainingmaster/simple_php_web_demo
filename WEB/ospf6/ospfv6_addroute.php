<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title> ospf6</title>
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];
$network =$_POST['network'];
$area =$_POST['area'];
$check_result = 1;

$area_piece = explode(".",$area);
if(count($area_piece) != 4 )
	$check_result = 0;
else
{
	for($i=0;$i<4;$i++)
	{
		if(!is_numeric($area_piece[$i]))
			$check_result = 0;
	}
}

	$prefix1 = strstr($network, '/',true); 
	$length1 = substr(strstr($network, '/'),1);


if($check_result==1 && strpos($prefix1,":") && is_numeric($length1))
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.191.3.2.0","s","1|router ospf6");
snmpset($Board_IP,"public",".1.3.6.1.2.1.191.3.2.0","s","2|area $area range $network");
snmpset($Board_IP,"public",".1.3.6.1.2.1.191.3.2.0","s","1|write memory");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ospf6_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

