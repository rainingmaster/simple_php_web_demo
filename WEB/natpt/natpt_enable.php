<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
snmp_set_quick_print(1);
	$natptglobalstatus=$_POST['natptglobalstatus'];
	$Board_IP =$_GET['Board_IP'];
	if($natptglobalstatus==1)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.4.1.54323.1.5.0","i","1") == 1)
		{?>
	                <script>window.alert('使能Nat-PT成功!');</script>
	 	 	<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('使能Nat-PT失败!');</script>
	 	<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	else if($natptglobalstatus==2)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.4.1.54323.1.5.0","i","0") == 1)
		{?>
	                <script>window.alert('失能Nat-PT成功!');</script>
	 	 	<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('失能Nat-PT失败!');</script>
	 	<script>location.href="natpt_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
?>
</body>

