<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>ospf6 network delete</title>
</head>
<body>
<?php
$Board_IP =$_GET['Board_IP'];		
$ospfv6_delete =$_POST['ospfv6_delete'];
$piecesconf=explode("+",$ospfv6_delete);
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.191.3.2.0","s","1|router ospf6");
snmpset($Board_IP,"public",".1.3.6.1.2.1.191.3.2.0","s","2|no area $piecesconf[0] range $piecesconf[1]");
snmpset($Board_IP,"public",".1.3.6.1.2.1.191.3.2.0","s","1|write memory");
?>
<script>location.href="ospf6_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>
