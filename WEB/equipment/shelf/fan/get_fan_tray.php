<?php
include('../../atca.class.php');

if(isset($_GET['ipmb'])) {
	$ipmb = $_GET['ipmb'];
}
else {
	echo "wrong data!";
	exit;
}

	$json_str = "[";//[
	$shelf = new Device($ipmb, 1, "Fan_tray", "FRU");
	for($i = 0, $l = count($shelf->Fru_lst); $i < $l; $i++)
	{
		if($shelf->Fru_lst[$i]->Type == 4)
		{
			$json_str = $json_str.'{"Str_name":"'.$shelf->Fru_lst[$i]->Str_name.'",';
			$json_str = $json_str.'"Fru_id":"'.$shelf->Fru_lst[$i]->Fru_id.'",';
			$json_str = $json_str.'"Minfanlevel":"'.$shelf->Fru_lst[$i]->Minfanlevel.'",';
			$json_str = $json_str.'"Maxfanlevel":"'.$shelf->Fru_lst[$i]->Maxfanlevel.'",';
			$json_str = $json_str.'"Curfanlevel":"'.$shelf->Fru_lst[$i]->Curfanlevel.'"},';
		}
	}
	$json_str = substr($json_str, 0, -1)."]";//È¥µô¶ººÅ£¬¼ÓÉÏ]
	echo $json_str;
?>