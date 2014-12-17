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
$function =$_POST['function'];

if($function==1)
	$ini->iniWrite("board".$board_id, "function", "all");

if($function==2)
	$ini->iniWrite("board".$board_id, "function", "route");

if($function==3)
	$ini->iniWrite("board".$board_id, "function", "transition");

if($function==4)
	$ini->iniWrite("board".$board_id, "function", "none");

$ini->iniUpdate();
?>
<script>location.href="board_type.php"</script>
</body>

