<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>isisv6</title>
</head>
<body>
<?php
$Board_IP =$_GET['Board_IP'];		
$network = $_POST['network'];
$isisv6_id = $_POST['isisv6_id'];
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|interface $network");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","9|ipv6 router isis $isisv6_id");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|write memory");
?>
<script>location.href="isis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

