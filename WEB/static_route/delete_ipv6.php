<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>static route</title>
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$ptr = $_POST['delete_ipv4_ptr'];
snmp_set_quick_print(1);
$static_route_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.4.24.9.1.0");
$piecesconf=explode(" ",$static_route_information);
for($i = 0;$i < count($piecesconf);$i ++)
{
        if(strcmp($piecesconf[$i],"route")==0) 
        {
      		for($j=0,$k=0,$y=0;strcmp($piecesconf[$i+$j],"route")==0;$j=$j+3,$k++)
		{
			
			$net[$k] = $piecesconf[$i+$j+1];
			$temp = $piecesconf[$i+$j+2];

			$n = strpos($temp,'i');
                	if ($n) 
                		$temp1=substr($temp,0,$n);
			else
				$temp1=$temp;

			$m = strpos($temp1,'!');
                	if ($m) 
                		$address[$k]=substr($temp1,0,$m);
			else
				$address[$k]=$temp1;

			if(strpos($net[$k],':'))
			{
				$ipv6_net[$y] = $net[$k];
				$ipv6_address[$y] = $address[$k];
				$y++;
			}
                }
		$ipv6_net[$y] = "end";
		break;
        }
}

for($i = 0;strcmp($ipv6_net[$i] ,"end") != 0;$i++)
{
	if($i == $ptr)
	{
		$delete_route = $ipv6_net[$i];
		$delete_address = $ipv6_address[$i];
		break;
	}
}
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|no ipv6 route $delete_route $delete_address");
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|write");
?>
<script>location.href="static_route.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

