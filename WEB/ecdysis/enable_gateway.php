<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>Change board IVI Informatin</title>
</head>

<body>
<?php
$Board_IP =$_GET['Board_IP'];
snmp_set_quick_print(1);
	$number=$_POST['gatewayglobalstatus'];
	if($number==1)
	{
		if(snmpget($Board_IP,"public",".1.3.6.1.2.1.321.1.1.0")==0)
		{?>
	              	<script>window.alert('使能失败，请先配置相关参数！');</script>
	 	 	<script>location.href="ecdysis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		if(snmpset($Board_IP,"public",".1.3.6.1.2.1.321.1.2.0","i","1")==1)
		{?>
	                <script>window.alert('使能ECDYSIS成功!');</script>
	 	 	<script>location.href="ecdysis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('使能ECDYSIS失败!');</script>
	 	<script>location.href="ecdysis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	else if($number==2)
	{

		if(snmpset($Board_IP,"public",".1.3.6.1.2.1.321.1.2.0","i","0")==1)
		{?>
	                <script>window.alert('失能ECDYSIS成功!');</script>
	 	 	<script>location.href="ecdysis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('失能ECDYSIS失败!');</script>
	 	<script>location.href="ecdysis_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	snmpset($Board_IP,"public",".1.3.6.1.2.1.321.2.3.0","i","1");
?>
</body>

