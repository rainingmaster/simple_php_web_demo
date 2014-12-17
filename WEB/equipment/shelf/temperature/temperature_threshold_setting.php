<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
$Upper_non_critical_threshold = $_POST['Upper_non_critical_threshold'];
$Lower_critical_threshold = $_POST['Lower_critical_threshold'];
$temperature_id =$_POST['temperature_id'];
$ipmb = $_GET['ipmb'];

include('../../atca.class.php');

if(is_numeric($Upper_non_critical_threshold) && is_numeric($Lower_critical_threshold) && $Upper_non_critical_threshold>$Lower_critical_threshold )
{
		echo "正在设置，请稍后...";
		$sensor = new Sensor($ipmb, $temperature_id);
		$sensor->updataThreshold('', '', $Upper_non_critical_threshold, '', $Lower_critical_threshold, '');
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}
?>
<script>location.href="temperature_information.php?ipmb=<?php echo $ipmb; ?>"</script>
</body>

