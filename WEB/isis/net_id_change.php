<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>isisv4</title>
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$new_net_id = $_POST['net_id'];
snmp_set_quick_print(1);
$isisv4_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.316.1.1.0");
$piecesconf=explode(" ",$isisv4_information);

for($i=0,$k=0;$i < count($piecesconf);$i ++)
{
	if(strcmp($piecesconf[$i],"net")==0)
	{
		$old_net_id = $piecesconf[$i+1];
		break;
	}  
}

$router_with_net_flag = 0;
for($i=0,$k=0,$m=0;$i < count($piecesconf);$i ++)
{    
	if(strcmp($piecesconf[$i],"isis")==0) 
	{
		if(strcmp($piecesconf[$i-1],"router")!=0 && strcmp($piecesconf[$i+1],"circuit-type")!=0)
		{
			$router_isis[$k] = $piecesconf[$i+1];
			$k = $k + 1;
			if(strcmp($piecesconf[$i+2],"net")==0)
			{
				$router_with_net[$m] = $piecesconf[$i+1];
				$router_with_net_flag = 1;
				$m = $m + 1;
			}
		}
	}
}
$router_isis[$k] = "end";
$router_with_net[$m] = "end";

$check_result = 1;
$net_id_piece = explode(".",$new_net_id);
if(strlen($new_net_id) != 25)	
	$check_result = 0;
if(count($net_id_piece) != 6 )
	$check_result = 0;
else
{
	for($i=0;$i<6;$i++)
	{
		if(!is_numeric($net_id_piece[$i]))
			$check_result = 0;
	}
}

if($check_result == 1)
{
	if($router_with_net_flag == 1)
	{
		for($k = 0;strcmp($router_with_net[$k],"end") != 0;$k++)
		{
			snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|router isis $router_with_net[$k]");
			snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","2|no net $old_net_id");
		}
	}

	for($k = 0;strcmp($router_isis[$k],"end") != 0;$k++)
	{
		snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|router isis $router_isis[$k]");
		snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","2|net $new_net_id");
	}
	snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|write memory");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}

$net_id_success_flag = 0;
$isis_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.316.1.1.0");
$piecesconf=explode(" ",$isis_information);
for($i=0,$k=0;$i < count($piecesconf);$i ++)
{
	if(strcmp($piecesconf[$i],"net")==0)
	{
		$net_id_success_flag = 1;
		break;
	}
}

if($net_id_success_flag == 0)	
{
	?>
	<script>window.alert('无法将net号设置为<?php echo $new_net_id; ?>，请重新输入！');</script>
	<?php
}

?>
<script>location.href="isis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

