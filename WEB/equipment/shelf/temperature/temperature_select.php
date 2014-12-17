<html>
<head>
<title>ÎÂ¶È²Ëµ¥</title>
<style>
ul{float: left;width: 100%;padding: 0;margin: 0;list-style-type: none;}
a{float: left;width: 7em;text-decoration: none;color: black;background-color: #eeeeee;padding: 0.2em 0.6em;border-right: 2px solid white;}
a: hover{background-color: #eeeeee}
li{display: inline}
</style>
</head>
<body>

<?php
include('../../atca.class.php');
$atca = new Atca("NULL");
$liveDevice = $atca->getLiveLst("IPMC");
?>

<ul>
<?php
	for($n = 0; $n < count($liveDevice); $n++)
	{
		?>
			<li><a href="temperature_information.php?ipmb=<?php echo $liveDevice[$n]->Ipmc->Ipmb_addr; ?>&name=<?php echo substr($liveDevice[$n]->Ipmc->Str_name, 1, -1); ?>" target="frame6" ><?php echo substr($liveDevice[$n]->Ipmc->Str_name, 1, -1); ?></a></li>
		<?php
	}
?>
</ul>
</body>
</html>


