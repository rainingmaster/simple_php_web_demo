<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
include('../include/ssh2.php');

$Board_IP =$_GET['Board_IP'];
$protocol = $_GET['protocol'];
$command = $_GET['command'];
$natpt = $_GET['natpt'];
$nat64 = $_GET['nat64'];
$ivi = $_GET['ivi'];
$dslite = $_GET['dslite'];
$ecdysis = $_GET['ecdysis'];
$natpt_flag = 0;
$nat64_flag = 0;
$ivi_flag = 0;
$dslite_flag = 0;
$ecdysis_flag = 0;

if(strcmp($natpt,"运行") == 0)
	$natpt_flag = 1;
if(strcmp($nat64,"运行") == 0)
	$nat64_flag = 1;
if(strcmp($ivi,"运行") == 0)
	$ivi_flag = 1;
if(strcmp($dslite,"运行") == 0)
	$dslite_flag = 1;
if(strcmp($ecdysis,"运行") == 0)
	$ecdysis_flag = 1;

if($protocol == 1)
{
	if($command == 1 && $natpt_flag == 0)
		ssh2execu($Board_IP, 'natptd -d');
	
	if($command == 2 && $natpt_flag == 1)
		ssh2execu($Board_IP, 'killall natptd');	

}
if($protocol == 2)
{
	if($command == 1 && $nat64_flag == 0)
		ssh2execu($Board_IP, 'nat64d -d');	
	
	if($command == 2 && $nat64_flag == 1)
		ssh2execu($Board_IP, 'killall nat64d');	
}
if($protocol == 3)
{
	if($command == 1 && $ivi_flag == 0)
		ssh2execu($Board_IP, 'ivid -d');	
	
	if($command == 2 && $ivi_flag == 1)
		ssh2execu($Board_IP, 'killall ivid');		
}
if($protocol == 4)
{
	if($command == 1 && $dslite_flag == 0)
		ssh2execu($Board_IP, 'dslited -d');
	
	if($command == 2 && $dslite_flag == 1)
		ssh2execu($Board_IP, 'killall dslited');	
}
if($protocol == 5)
{
	if($command == 1 && $ecdysis_flag == 0)
		ssh2execu($Board_IP, 'ecdysisd -d');
	
	if($command == 2 && $ecdysis_flag == 1)
		ssh2execu($Board_IP, 'killall ecdysisd');	
}
?>
<script>location.href="transition_overall.php"</script>
</body>

