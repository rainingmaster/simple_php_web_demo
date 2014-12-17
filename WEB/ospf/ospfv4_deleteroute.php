<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>rip network delete</title>
</head>
<body>
<?php
$Board_IP =$_GET['Board_IP'];		
$ospfv4_delete =$_POST['ospfv4_delete'];
$piecesconf=explode("+",$ospfv4_delete);
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.14.16.2.0","s","1|router ospf");
snmpset($Board_IP,"public",".1.3.6.1.2.1.14.16.2.0","s","2|no network $piecesconf[0] area $piecesconf[1]");
snmpset($Board_IP,"public",".1.3.6.1.2.1.14.16.2.0","s","1|write memory");
?>
<script>location.href="ospf_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>
