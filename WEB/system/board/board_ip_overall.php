<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>网卡信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white;}
#main{width: 600px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 100%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 30px;line-height: 35px;padding: 0 20px;border-bottom: 1px solid #aaaaaa;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
.no-border td{border-bottom: none;box-shadow: none;-moz-box-shadow: none;-webkit-box-shadow: none;}
.col-cell{text-align: center;width: 100px;font: normal 1em Verdana, Arial, Helvetica;}
.col-cell1, .col-cell2{background: #efefef;background: rgba(144,144,144,0.15);border-right: 1px solid white;}
.col-cell3{background: #e7f3d4;background: rgba(184,243,85,0.3);}
.col-cellh{font: bold 1.3em 'trebuchet MS', 'Lucida Sans', Arial;-moz-border-radius-topright: 10px;-moz-border-radius-topleft: 10px;border-top-right-radius: 10px;border-top-left-radius: 10px;border-top: 1px solid #eaeaea !important;}
.col-cellf{font: bold 1.4em Georgia;-moz-border-radius-bottomright: 10px;-moz-border-radius-bottomleft: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;border-bottom: 1px solid #dadada !important;}
</style>
<style>
body { background:#f5f5f5;}
</style>
</head>
<body>
<?php
include('../../include/ssh2.php');
include('interface_list.php');
$ini = new ini();
$board_count = $ini->iniRead("device_info", "board_count");

	for($i = 1; $i <= $board_count; $i++)
	{
		$Board_IP = $ini->iniRead("board".$i, "ipaddr");
		$result[$i] = ssh2execu($Board_IP, 'ip address');
		$piecesconf=explode(" ",$result[$i]);
	
		for($j = 0;$j < count($board_eth[$i]);$j ++)
		{
			for($k = 0;$k < count($piecesconf);$k ++)
			{
				if(strcmp($piecesconf[$k],$board_eth[$i][$j])==0)
					$ip_ptr[$i][$j] =  $k;	
			}
		}
		$ip_ptr[$i][$j] = count($piecesconf);

		for($j = 0;$j < count($board_eth[$i]);$j ++)
		{
				for($m=$ip_ptr[$i][$j],$p=0,$q=0;$m<$ip_ptr[$i][$j+1];$m++)
				{
		
					if(strcmp($piecesconf[$m],"inet")==0)
					{
						$ipv4_address[$i][$j][$p] = $piecesconf[$m+1];
						$p = $p +1;	
					}
						
					if(strcmp($piecesconf[$m],"inet6")==0)
					{
						$ipv6_address[$i][$j][$q] = $piecesconf[$m+1];
						$q=$q+1;
					}
				}
		}

	}

?>

<?php
for($i = 1; $i <= $board_count; $i++)
{
?>
<h4>&nbsp;&nbsp;板卡<?php echo $i; ?>的IP信息:<a href="board_ip.php?board_index=<?php echo $i; ?>&Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>"><font color=black>配置板卡<?php echo $i; ?>的IP</a></h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1">网卡名称</td>
		<td class="col-cell col-cell1">IPv4地址</td>
		<td class="col-cell col-cell1">IPv6地址</td>
	</tr>
	<?php
	for($j = 0;$j < count($board_eth[$i]);$j ++)
	{
	?>
	<tr>
		<td class="col-cell col-cell1" ><?php echo substr($board_eth[$i][$j],0,strlen($board_eth[$i][$j])-1); ?></td>
		<td class="col-cell col-cell1" >
		<?php
		if(count($ipv4_address[$i][$j]) == 0)
			echo "未配置";
		else
		{
			for($p=0;$p<count($ipv4_address[$i][$j]);$p++)
			{
				echo $ipv4_address[$i][$j][$p];
				print("<br>");
			}
		}
		?>
		</td>
		<td class="col-cell col-cell1" >
		<?php
		if(count($ipv6_address[$i][$j]) == 0)
			echo "未配置";
		else
		{
			for($q=0;$q<count($ipv6_address[$i][$j]);$q++)
			{
				echo $ipv6_address[$i][$j][$q];
				print("<br>");
			}
		}
		?>
		</td>
	</tr>
	<?php
	}
	?>
</table>
</div>
<?php
}
?>

</body>
</html>