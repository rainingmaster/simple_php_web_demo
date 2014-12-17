<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>过渡协议进程管理</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 1300px;margin: 5px auto auto 5px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 500px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 90%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 30px;line-height: 35px;padding: 0 20px;border-bottom: 1px solid #cdcdcd;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
.no-border td{border-bottom: none;box-shadow: none;-moz-box-shadow: none;-webkit-box-shadow: none;}
.col-cell{text-align: center;width: 50px;font: normal 1em Verdana, Arial, Helvetica;}
.col-cell1, .col-cell2{background: #CCDDDD;border-right: 1px solid white;}
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
include('../include/ssh2.php');
$ini = new ini();

$board_count = $ini->iniRead("device_info", "board_count");

	for($i = 1; $i <= $board_count; $i++)
	{
		if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
		{
			$Board_IP = $ini->iniRead("board".$i, "ipaddr");
			$ping_result = shell_exec("ping -c 1 $Board_IP");
			if(strstr($ping_result,"1 received,"))
			{
			$ping_flag[$i] = 1;
			$result1 = ssh2execu($Board_IP, 'ps -e| grep natptd');
			$result2 = ssh2execu($Board_IP, 'ps -e| grep nat64d');
			$result3 = ssh2execu($Board_IP, 'ps -e| grep ivid');
			$result4 = ssh2execu($Board_IP, 'ps -e| grep dslited');
			$result5 = ssh2execu($Board_IP, 'ps -e| grep ecdysisd');

			if(strcmp(substr($result1, -7,6),"natptd")==0)
				$natpt_process_state[$i] = "运行";
			else
				$natpt_process_state[$i] = "关闭";

			if(strcmp(substr($result2, -7,6),"nat64d")==0)
				$nat64_process_state[$i] = "运行";
			else
				$nat64_process_state[$i] = "关闭";

			if(strcmp(substr($result3, -5,4),"ivid")==0)
				$ivi_process_state[$i] = "运行";
			else
				$ivi_process_state[$i] = "关闭";

			if(strcmp(substr($result4, -8,7),"dslited")==0)
				$dslite_process_state[$i] = "运行";
			else
				$dslite_process_state[$i] = "关闭";

			if(strcmp(substr($result5, -9,8),"ecdysisd")==0)
				$ecdysis_process_state[$i] = "运行";
			else
				$ecdysis_process_state[$i] = "关闭";
			}
			else
				$ping_flag[$i] = 0;
		}
	}
?>


<h4>过渡协议进程管理:</h4>
<table class="features-table">
	 <tr>
		<td class="col-cell col-cell1" >板卡IP</td>
		<td class="col-cell col-cell1" >Nat-PT</td>
		<td class="col-cell col-cell1" >NAT64</td>
		<td class="col-cell col-cell1" >IVI</td>
		<td class="col-cell col-cell1" >DS-Lite</td>
		<td class="col-cell col-cell1" >ECDYSIS</td>	
	 </tr>
	<?php 
	for($i = 1; $i <= $board_count; $i++)
	{
		if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
		{
			if($ping_flag[$i] == 1)
			{
	?>
	 <tr>
		<td class="col-cell col-cell1" ><?php echo $ini->iniRead("board".$i, "ipaddr"); ?></td>
		<td class="col-cell  col-cell1">
		<?php 
		if(strcmp($natpt_process_state[$i],"运行") == 0)
		{
			echo $natpt_process_state[$i]; 
		?>
			<a href="../natpt/natpt_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>"><font color=black>配置</a>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=1&command=2&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>终止进程</a>
		<?php
		}
		else
		{
			echo $natpt_process_state[$i]; 
		?>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=1&command=1&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>开启进程</a>
		<?php
		}
		?>
		</td>	
		<td class="col-cell  col-cell1">
		<?php 
		if(strcmp($nat64_process_state[$i],"运行") == 0)
		{
			echo $nat64_process_state[$i]; 
		?>
			<a href="../nat64/nat64_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>"><font color=black>配置</a>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=2&command=2&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>终止进程</a>
		<?php
		}
		else
		{
			echo $nat64_process_state[$i]; 
		?>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=2&command=1&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>开启进程</a>
		<?php
		}
		?>
		</td>
		<td class="col-cell  col-cell1">
		<?php 
		if(strcmp($ivi_process_state[$i],"运行") == 0)
		{
			echo $ivi_process_state[$i]; 
		?>
			<a href="../ivi/ivi_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>"><font color=black>配置</a>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=3&command=2&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>终止进程</a>
		<?php
		}
		else
		{
			echo $ivi_process_state[$i]; 
		?>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=3&command=1&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>开启进程</a>
		<?php
		}
		?>
		</td>	
		<td class="col-cell  col-cell1">
		<?php 
		if(strcmp($dslite_process_state[$i],"运行") == 0)
		{
			echo $dslite_process_state[$i]; 
		?>
			<a href="../dslite/dslite_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>"><font color=black>配置</a>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=4&command=2&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>终止进程</a>
		<?php
		}
		else
		{
			echo $dslite_process_state[$i]; 
		?>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=4&command=1&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>开启进程</a>
		<?php
		}
		?>
		</td>
		<td class="col-cell  col-cell1">
		<?php 
		if(strcmp($ecdysis_process_state[$i],"运行") == 0)
		{
			echo $ecdysis_process_state[$i]; 
		?>
			<a href="../ecdysis/ecdysis_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>"><font color=black>配置</a>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=5&command=2&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>终止进程</a>
		<?php
		}
		else
		{
			echo $ecdysis_process_state[$i]; 
		?>
			<a href="process_control.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>&protocol=5&command=1&natpt=<?php echo $natpt_process_state[$i]; ?>&nat64=<?php echo $nat64_process_state[$i]; ?>&ivi=<?php echo $ivi_process_state[$i]; ?>&dslite=<?php echo $dslite_process_state[$i]; ?>&ecdysis=<?php echo $ecdysis_process_state[$i]; ?>"><font color=black>开启进程</a>
		<?php
		}
		?>
		</td>				
	 </tr>
	<?php 
			}
			else
			{
			?>
	 		<tr>
				<td class="col-cell col-cell1" ><?php echo $ini->iniRead("board".$i, "ipaddr"); ?></td>
				<td class="col-cell col-cell1" >板卡无法连通</td>
				<td class="col-cell col-cell1" >板卡无法连通</td>
				<td class="col-cell col-cell1" >板卡无法连通</td>
				<td class="col-cell col-cell1" >板卡无法连通</td>
				<td class="col-cell col-cell1" >板卡无法连通</td>
			</tr>
			<?php 
			}
		}

	}
	?>
</table>

<h4>板卡功能说明：</h4>
<table class="features-table">
	 <tr>
		<td class="col-cell col-cell1" >板卡名称</td>
		<td class="col-cell col-cell1" >板卡IP</td>
		<td class="col-cell col-cell1" >类型</td>
		<td class="col-cell col-cell1" >路由协议</td>
		<td class="col-cell col-cell1" >过渡协议</td>
	 </tr>
	<?php 
	for($i = 1; $i <= $board_count; $i++)
	{
	?>
	<tr>
	<td class="col-cell col-cell1" >board<?php echo $i; ?></td>
	<td class="col-cell col-cell1" ><?php echo $ini->iniRead("board".$i, "ipaddr"); ?></td>
	<td class="col-cell col-cell1" >
	<?php 
		switch($ini->iniRead("board".$i, "type")) 
		{
			case "compute":
				echo "计算板";
				break;
			case "switch":
				echo "交换板";
				break;
			default:
				echo "类型有问题";
		}
	?>
	</td>
	<td class="col-cell col-cell1" >
	<?php 
		if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
			echo "可以配置";
		else
			echo "不可配置";

	?>
	</td>
	<td class="col-cell col-cell1" >
	<?php 
		if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
			echo "可以配置";
		else
			echo "不可配置";

	?>
	</td>
	</tr>
	<?php 
	}
	?>
</table>

</body>
</html>

