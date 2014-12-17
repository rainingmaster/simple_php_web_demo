<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php		
$ipmb = $_GET['ipmb'];
$fru_id = $_GET['fru_id'];
$control = $_GET['control'];

echo "正在设置，请稍后...";

include('../atca.class.php');
//这里第三个参数要设置为false，不去读取传感器信息，否则关闭之后无法再次开启
$fru = new Fru($ipmb, $fru_id);
if($control == 1)
	$fru->startFru();
if($control == 0)
	$fru->shutdownFru();
?>
<script>location.href="overall.php"</script>
</body>

