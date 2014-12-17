<?php
header('Content-type: text/html; charset=gb2312');
if(isset($_GET['ipmb'])&&isset($_GET['id'])) {
	$ipmb = $_GET['ipmb'];
	$id = $_GET['id'];
}
else {
	echo "data is wrong!";
	exit;
}

include('../atca.class.php');
$sensor = new Sensor($ipmb, $id);
$sensor->loadThreshold();
$json_obj = json_encode($sensor->Threshold);
echo $json_obj;
?>