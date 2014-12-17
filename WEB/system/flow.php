<?php
header('Content-type: text/html; charset=gb2312');
include("flow.class.php");
if (isset($_GET['ip'])) {
	$ip = $_GET['ip'];
	}
else {
	echo "wrong data";
	exit;
	}

	$flow = new flow($ip);
	$json_obj = json_encode($flow->interfaces);
	echo $json_obj;
?>