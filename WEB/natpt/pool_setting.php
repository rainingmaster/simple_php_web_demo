<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php		
function check_pool($start,$end)
{
	$flag = 1;
	if(filter_var($start, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$flag = 1;
	else
	{
		$flag = 0;
		return 0;
	}

	if(filter_var($end, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$flag = 1;
	else
	{
		$flag = 0;
		return 0;
	}
	
	$piece_start = explode(".",$start);
	$piece_end = explode(".",$end);
	if($piece_start[3] > $piece_end[3])
	{
		$flag = 0;
		return 0;
	}
	return $flag;
}
$Board_IP =$_GET['Board_IP'];
$Pool_start =$_POST['Pool_start'];
$Pool_end =$_POST['Pool_end'];
$result = check_pool($Pool_start,$Pool_end);

if($result==1)
{
snmp_set_quick_print(1);
snmpset($Board_IP, "public",".1.3.6.1.4.1.54323.1.1.0","a","$Pool_start");
snmpset($Board_IP, "public",".1.3.6.1.4.1.54323.1.2.0","a","$Pool_end");
}
else
{
?>
<script>window.alert('地址池输入有误，请重新配置！');</script>
<?php
}
?>
<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

