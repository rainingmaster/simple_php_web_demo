<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>rip network delete</title>
</head>
<body>
<?php		
$ripv4_net_delete =$_POST['ripv4_net_delete'];
$Board_IP = $_GET['Board_IP'];
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.23.6.2.0","s","2|no network $ripv4_net_delete");
snmpset($Board_IP,"public",".1.3.6.1.2.1.23.6.2.0","s","1|write memory");
?>
<script>location.href="rip_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>