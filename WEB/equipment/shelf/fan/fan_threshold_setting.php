<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
$Upper_critical_threshold = $_POST['Upper_critical_threshold'];
$fan_id =$_POST['fan_id'];
$ipmb = $_GET['ipmb'];
include('../../atca.class.php');

if(is_numeric($Upper_critical_threshold))
{

		echo "�������ã����Ժ�...";
		$sensor = new Sensor($ipmb, $fan_id);
		$sensor->updataThreshold('', $Upper_critical_threshold, '', '', '', '');
}
else
{
	?>
	<script>window.alert('���������������������ã�');</script>
	<?php
}

?>
<script>location.href="fan_information.php?ipmb=<?php echo $ipmb; ?>"</script>
</body>

