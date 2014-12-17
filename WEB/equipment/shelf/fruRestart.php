<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
$ipmb = $_GET['ipmb'];	
$type = $_GET['type'];	
$slot = $_GET['slot'];

include('../atca.class.php');

		echo "正在设置，请稍后...";

		if($type == "board") {
			$board = new Board(true, $ipmb, 0);
			$board->resetBoard();
		}
		else if($type == "shmc") {
			$shmc = new Shmc(true, $ipmb, 0);
			$shmc->resetShmc();
		}

?>
<script>location.href="overall.php"</script>
</body>

