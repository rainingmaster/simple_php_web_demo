<?php
include("../equipment/flow.class.php");

$test = new flow("192.168.82.5");
for ($i = 0, $l = count($test->interfaces); $i < $l; $i++) {
	var_dump($test->interfaces[$i]);
	echo "<br/>";
}
?>

