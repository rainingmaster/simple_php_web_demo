<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>ecdysis gateway setting</title>
</head>

<body>
<?php
	$Board_IP =$_GET['Board_IP'];
	$IPv6Prefix=$_POST['IPv6Prefix'];
	$AFTRipv6=$_POST['AFTRipv6'];
	$AFTRipv4=$_POST['AFTRipv4'];
	$dslitepool=$_POST['dslitepool'];
	$check_result = 1;
	$ipv6_check_result1 = 1;
	$ipv6_check_result2 = 1;

	$prefix1 = strstr($IPv6Prefix, '/',true); 
	$length1 = substr(strstr($IPv6Prefix, '/'),1);
	if($length1<1 || $length1>128)
		$ipv6_check_result1 = 0;

	$prefix2 = strstr($AFTRipv6, '/',true); 
	$length2 = substr(strstr($AFTRipv6, '/'),1);
	if($length2<1 || $length2>128)
		$ipv6_check_result2 = 0;

	$net1 = strstr($AFTRipv4, '/',true); 
	$length3 = substr(strstr($AFTRipv4, '/'),1);
	if(!filter_var($net1, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$check_result = 0;
	if($length3<1 || $length3>32)
		$check_result = 0;	

	$net2 = strstr($dslitepool, '/',true); 
	$length4 = substr(strstr($dslitepool, '/'),1);
	if(!filter_var($net2, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$check_result = 0;	
	if($length4<1 || $length4>32)
		$check_result = 0;	
	
if($check_result==1 && strpos($prefix1,":") && strpos($prefix2,":") && $ipv6_check_result1==1 && $ipv6_check_result2==1)
{
        snmp_set_quick_print(1);
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","1|no gateway dslite");
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","1|gateway dslite");
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite acl6 $IPv6Prefix");
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite ipv6 address $AFTRipv6");
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite ip address $AFTRipv4");
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite pool $dslitepool");
        snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite enable");
	snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","1|write");
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="dslite_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>


