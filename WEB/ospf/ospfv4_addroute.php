<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>rip network delete</title>
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

	$net1 = strstr($network, '/',true); 
	$length1 = substr(strstr($network, '/'),1);
	if(!filter_var($net1, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$check_result = 0;	


if($check_result == 1)
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.14.16.2.0","s","1|router ospf");
snmpset($Board_IP,"public",".1.3.6.1.2.1.14.16.2.0","s","2|network $network area $area");
snmpset($Board_IP,"public",".1.3.6.1.2.1.14.16.2.0","s","1|write memory");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ospf_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

