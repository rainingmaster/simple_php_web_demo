<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
/*
function ipv6_process($ip)
{
	$four_zero = "0000";
	$ip_process = explode("::",$ip);

	if(count($ip_process) >1 )
	{
		if( $ip_process[1] == '') 
		{
			$ip_process1 = explode(":",$ip_process[0]);
			$count1 = 8 - count($ip_process1);
			for($i = 0;$i < $count1; $i++)
				$ip_process[0] = $ip_process[0].":".$four_zero;
		}	
		else
		{
			$ip_process1 = explode(":",$ip_process[0]);
			$count2 = 7 - count($ip_process1);
			for($i = 0;$i < $count2; $i++)
				$ip_process[0] = $ip_process[0].":".$four_zero;
			$ip_process[0] = $ip_process[0].":".$ip_process[1];
		}
		return 	$ip_process[0];
	}
	else
		return $ip;
}

function prefix_match_length($prefix,$length)
{
	$prefix_arr = explode(":",$prefix);
	$add_zero = "0";
	$result = 1;
	$not_zero= array("1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","A","B","C","D","E","F");

	if(count($prefix_arr) >1)
	{
	for($i=0;$i<8;$i++)
	{
		$len1 = strlen($prefix_arr[$i]);
		for($j=0;$j<4-$len1;$j++)
			$prefix_arr[$i] = $add_zero."".$prefix_arr[$i];		
	}
	
	$prefix_process = $prefix_arr[0]."".$prefix_arr[1]."".$prefix_arr[2]."".$prefix_arr[3]."".$prefix_arr[4]."".$prefix_arr[5]."".$prefix_arr[6]."".$prefix_arr[7];
	$count = ($length-128)/4;
	$prefix_check = substr($prefix_process,$count);
	
	for($i=0;$i<strlen($prefix_check);$i++)
	{
		if(in_array(substr($prefix_check,$i,1), $not_zero))
			$result = 0;
	}
	return $result;
	}
	else
		return 0;
}
*/

$Board_IP =$_GET['Board_IP'];	
$IPv6_prefix =$_POST['IPv6_prefix'];
$IPv6_prefix1 = str_replace("FF", "ff", $IPv6_prefix);
//$IPv6_prefix_len ="40";
//$IPv6_prefix_process = ipv6_process($IPv6_prefix);
//$prefix_match_length_result = prefix_match_length($IPv6_prefix_process,$IPv6_prefix_len);

if(strpos($IPv6_prefix,":"))
{
	snmp_set_quick_print(1);
	snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.2.1.2.0","s","$IPv6_prefix1");
}
else
{
	?>
	<script>window.alert('IPv6前缀输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>


