<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>ecdysis gateway setting</title>
</head>
<body>
<?php	
	$Board_IP =$_GET['Board_IP'];
	$IPv4Address=$_POST['IPv4Address'];
	$PrefixAddress=$_POST['PrefixAddress'];
	$my_prefix = strstr($PrefixAddress, '/',true); 
	$my_length = substr(strstr($PrefixAddress, '/'),1);
	$check_result = 1;
	if($my_length>128 || $my_length<1)
		$check_result = 0;		

if(filter_var($IPv4Address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && strpos($my_prefix,":") && $check_result==1)
{
   	snmp_set_quick_print(1);
       	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.1.2.0","i","0");
       	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.1.1.0","i","1");
       	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.2.1.0","a","$IPv4Address");
       	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.2.2.0","s","$PrefixAddress");
      	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.1.2.0","i","1");
       	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.2.3.0","i","1");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ecdysis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

