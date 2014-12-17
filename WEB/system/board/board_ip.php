<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>网卡信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white;}
#main{width: 600px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
$Board_IP =$_GET['Board_IP'];
$board_index =$_GET['board_index'];
include('../../include/ssh2.php');
include('interface_list.php');
$ini = new ini();
$board_count = $ini->iniRead("device_info", "board_count");
	
$result= ssh2execu($Board_IP, 'ip address');
$piecesconf=explode(" ",$result);
	
		for($j = 0;$j < count($board_eth[$board_index]);$j ++)
		{
			for($k = 0;$k < count($piecesconf);$k ++)
			{
				if(strcmp($piecesconf[$k],$board_eth[$board_index][$j])==0)
					$ip_ptr[$j] =  $k;	
			}
		}
		$ip_ptr[$j] = count($piecesconf);

		for($j = 0;$j < count($board_eth[$board_index]);$j ++)
		{
				for($m=$ip_ptr[$j],$p=0,$q=0;$m<$ip_ptr[$j+1];$m++)
				{
		
					if(strcmp($piecesconf[$m],"inet")==0)
					{
						$ipv4_address[$j][$p] = $piecesconf[$m+1];
						$p = $p +1;
						
					}	
					
					if(strcmp($piecesconf[$m],"inet6")==0)
					{
						$ipv6_address[$j][$q] = $piecesconf[$m+1];
						$q=$q+1;
					}
				}
		}
		

$ipv4_array = array();
$ipv6_array = array();
snmp_set_quick_print(1);
$rtm_ip_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.4.24.9.1.0");
$piecesconf1 = explode(" ",$rtm_ip_information);
for($j=0,$x=0,$y=0;$j<count($piecesconf1);$j++)
{
	if(strcmp($piecesconf1[$j],"ip")==0 && strcmp($piecesconf1[$j+1],"address")==0) 
	{
		$rtm_ipv4[$x] = $piecesconf1[$j+2];
		$rtm_ipv4[$x] = strstr($rtm_ipv4[$x], '/',true); 
		array_push($ipv4_array, $rtm_ipv4[$x]);
		$x = $x +1;
	}

	if(strcmp($piecesconf1[$j],"ipv6")==0 && strcmp($piecesconf1[$j+1],"address")==0) 
	{
		$rtm_ipv6[$y] = $piecesconf1[$j+2];
		$rtm_ipv6[$y] = strstr($rtm_ipv6[$y], '/',true); 
		array_push($ipv6_array, $rtm_ipv6[$y]);
		$y = $y +1;
	}
}
		$no_ipv4_delelte_flag = 1;
		for($j = 0,$a=0;$j < count($board_eth[$board_index]);$j ++)
		{		
			for($p=0;$p<count($ipv4_address[$j]);$p++)
			{
				if(in_array(strstr($ipv4_address[$j][$p], '/',true),$ipv4_array))
					$no_ipv4_delelte_flag = 0;
				else
				{
					$forbid_delete_ipv4[$a] = $ipv4_address[$j][$p];
					$a=$a+1; 
				}
			}
		}

		$no_ipv6_delelte_flag = 1;
		for($j = 0,$a=0;$j < count($board_eth[$board_index]);$j ++)
		{		
			for($p=0;$p<count($ipv6_address[$j]);$p++)
			{
				if(in_array(strstr($ipv6_address[$j][$p], '/',true),$ipv6_array))
					$no_ipv6_delelte_flag = 0;
				else
				{
					$forbid_delete_ipv6[$a] = $ipv6_address[$j][$p];
					$a=$a+1; 
				}
			}
		}
?>

<h4>&nbsp;&nbsp;配置board<?php echo $board_index; ?>的IPv4地址:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="add_ipv4.php?Board_IP=<?php echo $Board_IP; ?>&board_index=<?php echo $board_index; ?>" >
	<tr>
		<td class="col-cell" >添加IPv4地址</td>
		<td class="col-cell col-cell1">
		网卡：<select name="board_eth">
		<?php
		for($j = 0; $j < count($board_eth[$board_index]); $j++) 
		{
		?>
		<option value=<?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?> ><?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?></option>
		<?php
		}
		?>
		</select>&nbsp;
		地址：<input type="text" name="ipv4_address" id="ipv4_address" class="txt" value="110.110.110.10/24" style="color:#3C3C3C;"  onfocus="if(value=='110.110.110.10/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='110.110.110.10/24'}"/>
		<input  type="submit" value="添加" >
              </td>
	</tr>
</form>

	<tr>
		<td class="col-cell" >删除IPv4地址</td>
		<td class="col-cell col-cell1">
		<?php
		if($no_ipv4_delelte_flag == 1)
			echo "没有可删除的IPv4地址";
		else
		{
		?>
		<form name="form" method="post" action="delete_ipv4.php?Board_IP=<?php echo $Board_IP; ?>&board_index=<?php echo $board_index; ?>" >
	       	<select name="ipv4_delete">
              		<?php
			for($j = 0;$j < count($board_eth[$board_index]);$j ++)
			{
				for($p=0;$p<count($ipv4_address[$j]);$p++)
				{
				if(in_array(strstr($ipv4_address[$j][$p], '/',true),$ipv4_array))
					{
					
					$arr = array(substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1),$ipv4_address[$j][$p]);
					$interface_and_ipv4 =implode("+",$arr);
			?>
	       		<option value= <?php echo $interface_and_ipv4; ?> >  网卡:<?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?> IP:<?php echo $ipv4_address[$j][$p]; ?> </option>
			<?php
					}
				}
 			}
			?>
	      	 </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 <input  type="submit" value="删除" ></form>
		<?php
		}
		?>
                </td>
	</tr>
	<tr>
		<td class="col-cell" >禁止删除的地址</td>
		<td class="col-cell col-cell1">
		<?php
		for($a=0;$a<count($forbid_delete_ipv4);$a++)
		{
			echo $forbid_delete_ipv4[$a];
			print("<br>");
		}
		?>
              </td>
	</tr>
</table>
</div>

<h4>&nbsp;&nbsp;配置board<?php echo $board_index; ?>的IPv6地址:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="add_ipv6.php?Board_IP=<?php echo $Board_IP; ?>&board_index=<?php echo $board_index; ?>" >
	<tr>
		<td class="col-cell" >添加IPv6地址</td>
		<td class="col-cell col-cell1">
		网卡：<select name="ipv6_board_eth">
		<?php
		for($j = 0; $j < count($board_eth[$board_index]); $j++) 
		{
		?>
		<option value=<?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?> ><?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?></option>
		<?php
		}
		?>
		</select>&nbsp;
		地址：<input type="text" name="ipv6_address" id="ipv6_address" class="txt" size="30" value="2001::/40" style="color:#3C3C3C;"  onfocus="if(value=='2001::/40'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::/40'}"/>
		<input  type="submit" value="添加" >
              </td>
	</tr>
</form>

	<tr>
		<td class="col-cell" >删除IPv6地址</td>
		<td class="col-cell col-cell1">
		<?php
		if($no_ipv6_delelte_flag == 1)
			echo "没有可删除的IPv6地址";
		else
		{
		?>
	        <form name="form" method="post" action="delete_ipv6.php?Board_IP=<?php echo $Board_IP; ?>&board_index=<?php echo $board_index; ?>" >
	       	<select name="ipv6_delete">
              		<?php
			for($j = 0;$j < count($board_eth[$board_index]);$j ++)
			{
				
				for($p=0;$p<count($ipv6_address[$j]);$p++)
				{
				if(in_array(strstr($ipv6_address[$j][$p], '/',true),$ipv6_array))
					{
					
					$arr = array(substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1),$ipv6_address[$j][$p]);
					$interface_and_ipv6 =implode("+",$arr);
			?>
	       		<option value= <?php echo $interface_and_ipv6; ?> >  网卡:<?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?> IP:<?php echo $ipv6_address[$j][$p]; ?> </option>
			<?php
					}
				}
 			}
			?>
	      	 </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 <input  type="submit" value="删除" ></form>
		<?php
		}
		?>
                </td>
	</tr>
	<tr>
		<td class="col-cell" >禁止删除的地址</td>
		<td class="col-cell col-cell1">
		<?php
		for($a=0;$a<count($forbid_delete_ipv6);$a++)
		{
			echo $forbid_delete_ipv6[$a];
			print("<br>");
		}
		?>
               </td>
	</tr>
</table>
</div>

<h4>&nbsp;&nbsp;board<?php echo $board_index; ?>的IP信息:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1">网卡名称</td>
		<td class="col-cell col-cell1">IPv4地址</td>
		<td class="col-cell col-cell1">IPv6地址</td>
	</tr>
	<?php
	for($j = 0;$j < count($board_eth[$board_index]);$j ++)
	{
	?>
	<tr>
		<td class="col-cell col-cell1" ><?php echo substr($board_eth[$board_index][$j],0,strlen($board_eth[$board_index][$j])-1); ?></td>
		<td class="col-cell col-cell1" >
		<?php
		if(count($ipv4_address[$j]) == 0)
			echo "未配置";
		else
		{
			for($p=0;$p<count($ipv4_address[$j]);$p++)
			{
				echo $ipv4_address[$j][$p];
				print("<br>");
			}
		}
		?>
		</td>
		<td class="col-cell col-cell1" >
		<?php
		if(count($ipv6_address[$j]) == 0)
			echo "未配置";
		else
		{
			for($q=0;$q<count($ipv6_address[$j]);$q++)
			{
				echo $ipv6_address[$j][$q];
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


</body>
</html>