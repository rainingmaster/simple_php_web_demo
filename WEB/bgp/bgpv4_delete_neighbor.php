<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>bgpv4 network delete</title>
</head>
<body>
<?php	
$bgp_id =$_GET['bgp_id'];	
$bgpv4_delete_neighbor =$_POST['bgpv4_delete_neighbor'];
$Board_IP =$_GET['Board_IP'];
$piecesconf=explode("+",$bgpv4_delete_neighbor);
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|router bgp $bgp_id");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","2|no neighbor $piecesconf[0] remote-as $piecesconf[1]");
snmpset($Board_IP,"public",".1.3.6.1.2.1.15.9.2.0","s","1|write memory");
?>
<script>location.href="bgp_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>
