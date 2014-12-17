<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
	snmp_set_quick_print(1);
	$iviglobalstatus=$_POST['iviglobalstatus'];
	$Board_IP =$_GET['Board_IP'];
	if($iviglobalstatus==1)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.4.1.54321.1.8.0","i","1") == 1)
		{?>
	                <script>window.alert('使能IVI成功!');</script>
	 	 	<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('使能IVI失败!');</script>
	 	<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	else if($iviglobalstatus==2)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.4.1.54321.1.8.0","i","0") == 1)
		{?>
	                <script>window.alert('失能IVI成功!');</script>
	 	 	<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('失能IVI失败!');</script>
	 	<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
?>
</body>

