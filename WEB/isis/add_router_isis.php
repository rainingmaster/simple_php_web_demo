<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
$Board_IP =$_GET['Board_IP'];	
$net = $_GET['net'];
$router_isis = $_POST['router_isis'];
$router_isis_type = $_POST['router_isis_type'];

if($router_isis_type==1)
	$is_type = "level-1";
if($router_isis_type==2)
	$is_type = "level-1-2";
if($router_isis_type==3)
	$is_type = "level-2-only";

snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|router isis $router_isis");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","2|net $net");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","2|is-type $is_type");
snmpset($Board_IP,"public",".1.3.6.1.2.1.316.1.2.0","s","1|write memory");
?>
<script>location.href="isis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

