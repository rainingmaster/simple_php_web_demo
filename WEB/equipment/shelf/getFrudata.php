<?php
header('Content-type: text/html; charset=gb2312');

if(isset($_GET['type'])) {
	$type = $_GET['type'];
	if(isset($_GET['ipmb']))
		$ipmb = $_GET['ipmb'];
}
else {
	echo "data is wrong!";
	exit;
}

include('../atca.class.php');
switch($type) {
	case "ALL":	
		$atca = new Atca("NULL");
		$json_obj = json_encode($atca->getPresentLst("FRU"));
		echo $json_obj;
		break;
	case "shmc":
	case "board":
	case "shelf":
		$device = new Device($ipmb, 1, $type, "FRU");
		$json_obj = json_encode($device->Fru_lst);
		echo $json_obj;
		break;
	default:
		echo "data is wrong!";
}
?>