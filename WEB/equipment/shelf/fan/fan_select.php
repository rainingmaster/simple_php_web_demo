<html>
<head>
<title>风扇菜单</title>
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
$shelf = $atca->loadShelf("IPMC");//仅仅非管理模块非板才有风扇
if(count($shelf) === 0 || $shelf[0]->Ipmc === false) {
	echo "机箱未接入，请检查！";
	echo "</body></html>";
	exit;
}
?>

<ul>
<?php
	for($n = 0; $n < count($shelf); $n++)
	{
		?>
			<li><a href="fan_information.php?ipmb=<?php echo $shelf[$n]->Ipmc->Ipmb_addr; ?>&name=<?php echo substr($shelf[$n]->Ipmc->Str_name, 1, -1); ?>" target="frame6" ><?php echo substr($shelf[$n]->Ipmc->Str_name, 1, -1); ?></a></li>
		<?php
	}
?>
</ul>
</body>
</html>


