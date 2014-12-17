<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
snmp_set_quick_print(1);
	$nat64globalstatus=$_POST['nat64globalstatus'];
	$Board_IP =$_GET['Board_IP'];
	if($nat64globalstatus==1)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.4.1.54322.1.3.0","i","1") == 1)
		{?>
	                <script>window.alert('使能NAT64成功!');</script>
	 	 	<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('使能NAT64失败!');</script>
	 	<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	else if($nat64globalstatus==2)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.4.1.54322.1.3.0","i","0") == 1)
		{?>
	                <script>window.alert('失能NAT64成功!');</script>
	 	 	<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('失能NAT64失败!');</script>
	 	<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
?>
</body>

