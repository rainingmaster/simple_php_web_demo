<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php		
include('../include/ssh2.php');
$ini = new ini();	
$hostname = $_POST['hostname'];
$Board_IP = $_GET['Board_IP'];
snmp_set_quick_print(1);
$result = snmpset($Board_IP,"public",".1.3.6.1.4.1.400.1.1.0","s","1|hostname $hostname");
$ini->iniWrite("isis--".$Board_IP, "hostname", $hostname);
$ini->iniUpdate();
?>
<script>location.href="isis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>