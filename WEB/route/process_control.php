<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php	
include('../include/ssh2.php');

//protocol代表要执行命令的协议：1-rip,2-ripng,3-ospf,4-ospf6,5-bgp,6-isis;command代表开启或关闭:1-开启，2-终止
$Board_IP =$_GET['Board_IP'];
$protocol = $_GET['protocol'];
$command = $_GET['command'];
$rip = $_GET['rip'];
$ripng = $_GET['ripng'];
$ospf = $_GET['ospf'];
$ospf6 = $_GET['ospf6'];
$bgp = $_GET['bgp'];
$isis = $_GET['isis'];
$rtm = $_GET['rtm'];
$rip_flag = 0;
$ripng_flag = 0;
$ospf_flag = 0;
$ospf6_flag = 0;
$bgp_flag = 0;
$isis_flag = 0;


if(strcmp($rip,"运行") == 0)
	$rip_flag = 1;
if(strcmp($ripng,"运行") == 0)
	$ripng_flag = 1;
if(strcmp($ospf,"运行") == 0)
	$ospf_flag = 1;
if(strcmp($ospf6,"运行") == 0)
	$ospf6_flag = 1;
if(strcmp($bgp,"运行") == 0)
	$bgp_flag = 1;
if(strcmp($isis,"运行") == 0)
	$isis_flag = 1;

if($protocol == 1)
{
	if($command == 1 && $rip_flag == 0)
		ssh2execu($Board_IP, 'ripd -d');
	
	if($command == 2 && $rip_flag == 1)
		ssh2execu($Board_IP, 'killall ripd');
}

if($protocol == 2)
{
	if($command == 1 && $ripng_flag == 0)
		ssh2execu($Board_IP, 'ripngd -d');
	
	if($command == 2 && $ripng_flag == 1)
		ssh2execu($Board_IP, 'killall ripngd');
}

if($protocol == 3)
{
	if($command == 1 && $ospf_flag == 0)
		ssh2execu($Board_IP, 'ospfd -d');
	
	if($command == 2 && $ospf_flag == 1)
		ssh2execu($Board_IP, 'killall ospfd');
}

if($protocol == 4)
{
	if($command == 1 && $ospf6_flag == 0)
		ssh2execu($Board_IP, 'ospf6d -d');
	
	if($command == 2 && $ospf6_flag == 1)
		ssh2execu($Board_IP, 'killall ospf6d');
}

if($protocol == 5)
{
	if($command == 1 && $bgp_flag == 0)
		ssh2execu($Board_IP, 'bgpd -d');
	
	if($command == 2 && $bgp_flag == 1)
		ssh2execu($Board_IP, 'killall bgpd');	
}

if($protocol == 6)
{
	if($command == 1 && $isis_flag == 0)
		ssh2execu($Board_IP, 'isisd -d');
	
	if($command == 2 && $isis_flag == 1)
		ssh2execu($Board_IP, 'killall isisd');	
}

?>
<script>location.href="route_overall.php"</script>
</body>

