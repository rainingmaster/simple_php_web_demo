<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
if(isset($_GET['ipmb'])) {
	$ipmb = $_GET['ipmb'];
	$fru_id = $_POST['fantray_id'];
	$level = $_POST['level'];
}
else {
	echo "wrong data!";
	exit;
}

include('../../atca.class.php');

if(is_numeric($level) && $level>4 && $level<16 )
{
		echo "正在设置，请稍后...";
		$fantray = new Fantray($ipmb, $fru_id, 1, 1, 1, 1, 1);
		$fantray->setCurFanlevel($level);
}
else
{
	?>
	<script>window.alert('参数输入有误，请重新配置！');</script>
	<?php
}


?>
<script>location.href="fan_information.php?ipmb=<?php echo $ipmb; ?>"</script>
</body>

