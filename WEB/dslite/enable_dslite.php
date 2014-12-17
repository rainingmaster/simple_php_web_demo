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
	$number=$_POST['dsliteglobalstatus'];
	if($number==1)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite enable") == 1)
		{?>
	                <script>window.alert('使能DS-Lite成功!');</script>
	 	 	<script>location.href="dslite_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('使能DS_lite失败,请检查参数是否配置正确!');</script>
	 	<script>location.href="dslite_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	else if($number==2)
	{
		if(snmpset($Board_IP,"public",".1.3.6.1.2.1.315.1.2.0","s","2|dslite disable") == 1)
		{?>
	                <script>window.alert('失能DS-Lite成功!');</script>
	 	 	<script>location.href="dslite_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
		else
		{?>
		<script>window.alert('失能DS-Lite失败!');</script>
	 	<script>location.href="dslite_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
		<?php }
	}
	snmpset("localhost","public",".1.3.6.1.2.1.315.1.2.0","s","1|write");
	?>
</body>

