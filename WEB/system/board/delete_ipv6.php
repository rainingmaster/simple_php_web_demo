<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>delete ipv6</title>
</head>
<body>
<?php
$ipv6_delete  = $_POST['ipv6_delete'];
$piecesconf =  explode("+",$ipv6_delete);
$Board_IP = $_GET['Board_IP'];
$board_index = $_GET['board_index'];
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|interface $piecesconf[0]");
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","3|no ipv6 address $piecesconf[1]");
snmpset($Board_IP,"public",".1.3.6.1.2.1.4.24.9.2.0","s","1|write");
?>
<script>location.href="board_ip.php?Board_IP=<?php echo $Board_IP; ?>&board_index=<?php echo $board_index; ?>"</script>
</body>
