<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>isisv6</title>
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$ptr = $_POST['isisv6_delete_interface_ptr'];

snmp_set_quick_print(1);
$isisv6_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.316.1.1.0");
$piecesconf=explode(" ",$isisv6_information);
for($i=0,$k=0;$i < count($piecesconf);$i ++)
{    
	if(strcmp($piecesconf[$i],"ipv6")==0 && strcmp($piecesconf[$i+1],"router")==0) 
	{
		$isisv6_interface[$k] = $piecesconf[$i-1];
		if(strcmp($piecesconf[$i-2],"isis") == 0)
			$isisv6_interface[$k] = $piecesconf[$i-5];

		$isisv6_id[$k] = substr($piecesconf[$i+3],0,1);
		if(strcmp(substr($piecesconf[$i+3],1,2),"!") != 0)
			$isisv6_id[$k] = substr($piecesconf[$i+3],0,2);
		$k = $k + 1;
		$no_isisv6_interface_flag = 0;
		
	}
}
$isisv6_interface[$k] = "end";

for($i = 0;strcmp($isisv6_interface[$i] ,"end") != 0;$i++)
{
	if($i == $ptr)
	{
		$delete_interface = $isisv6_interface[$i];
		$delete_id = $isisv6_id[$i];
		break;
	}
}

snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|interface $delete_interface");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","9|no ipv6 router isis $delete_id");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|write memory");
?>
<script>location.href="isis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

