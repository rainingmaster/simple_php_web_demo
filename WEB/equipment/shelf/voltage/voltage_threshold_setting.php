<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
$Upper_critical_threshold = $_POST['Upper_critical_threshold'];
$Lower_critical_threshold = $_POST['Lower_critical_threshold'];
$voltage_id =$_POST['voltage_id'];
$ipmb = $_GET['ipmb'];

include('../../atca.class.php');

if(is_numeric($Upper_critical_threshold) && is_numeric($Lower_critical_threshold) && $Upper_critical_threshold>$Lower_critical_threshold )
{
		echo "�������ã����Ժ�...";
		$sensor = new Sensor($ipmb, $voltage_id);
		$sensor->updataThreshold('', $Upper_critical_threshold, '', '', $Lower_critical_threshold, '');
}
else
{
	?>
	<script>window.alert('���������������������ã�');</script>
	<?php
}

?>
<script>location.href="voltage_information.php?ipmb=<?php echo $ipmb; ?>"</script>
</body>

