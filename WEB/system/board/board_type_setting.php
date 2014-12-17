<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
include('../../include/ssh2.php');
$ini = new ini();
$board_id = $_POST['board_id'];
$type =$_POST['type'];

if($type==1)
	$ini->iniWrite("board".$board_id, "type", "compute");

if($type==2)
	$ini->iniWrite("board".$board_id, "type", "switch");

$ini->iniUpdate();
?>
<script>location.href="board_type.php"</script>
</body>

