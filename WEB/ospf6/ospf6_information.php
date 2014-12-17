<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>OSPFv6状态信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 100%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 30px;line-height: 35px;padding: 0 60px;border-bottom: 1px solid #cdcdcd;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
.no-border td{border-bottom: none;box-shadow: none;-moz-box-shadow: none;-webkit-box-shadow: none;}
.col-cell{text-align: center;width: 200px;font: normal 1em Verdana, Arial, Helvetica;}
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
include('../include/ssh2.php');
$Board_IP =$_GET['Board_IP'];
$ini = new ini();
$hostname = $ini->iniRead("ospf6--".$Board_IP, "hostname");
$interface = $ini->iniRead("ospf6--".$Board_IP, "interface");
$interface_ip = $ini->iniRead("ospf6--".$Board_IP, "ip");

$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$result = ssh2execu($Board_IP, 'ps -e| grep ospf6d');
if(strcmp(substr($result, -7,6),"ospf6d")==0)
{
$no_ospfv6_route_flag = 1;
$no_ospfv6_interface_flag = 1;
$router_id = "路由ID尚未配置";
snmp_set_quick_print(1);
$ospfv6_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.191.3.1.0");
$piecesconf=explode(" ",$ospfv6_information);

for($i = 0;$i < count($piecesconf);$i ++)
{
        if(strcmp($piecesconf[$i],"router-id")==0)
        {
        	$no_ospfv6_routerid_flag = 0;
		$temp = $piecesconf[$i+1];
                $n = strpos($temp,'!');
                if ($n)
                	$router_id=substr($temp,0,$n);
                else
                        $router_id=$temp;
	}


	if(strcmp($piecesconf[$i],"area")==0  && strcmp($piecesconf[$i+2],"range")==0)  
        { 
      		for($j = 0,$k=0;strcmp($piecesconf[$i+$j],"area")==0 && strcmp($piecesconf[$i+$j+2],"range")==0;$j=$j+4,$k++)
		{
			$ospfv6_area[$k]=$piecesconf[$i+$j+1];
			$temp = $piecesconf[$i+$j+3];
			$n = strpos($temp,'!');
                	if ($n) 
                		$ospfv6_network[$k]=substr($temp,0,$n);
			else
				$ospfv6_network[$k]=$temp;
			$arr = array($ospfv6_area[$k],$ospfv6_network[$k]);
			$ospfv6_array[$k]=implode("+",$arr);
              }
      		$ospfv6_area[$k] = "end";
		$ospfv6_network[$k] = "end";
		$no_ospfv6_route_flag = 0;
		break;
        }
	 else
		$no_ospfv6_route_flag = 1;

}

for($i = 0;$i < count($piecesconf);$i ++)
{
	if(strcmp($piecesconf[$i],"interface")==0  && strcmp($piecesconf[$i+2],"area")==0)  
	{
		for($j = 0,$k=0;strcmp($piecesconf[$i+$j],"interface")==0 && strcmp($piecesconf[$i+$j+2],"area")==0;$j=$j+4,$k++)
		{
			$ospfv6_interface_name[$k]=$piecesconf[$i+$j+1];
			$temp = $piecesconf[$i+$j+3];
			$n = strpos($temp,'!');
                	if ($n) 
                		$ospfv6_interface_area[$k]=substr($temp,0,$n);
			else
				$ospfv6_interface_area[$k]=$temp;
			$arr = array($ospfv6_interface_name[$k],$ospfv6_interface_area[$k]);
			$ospfv6_interface[$k]=implode("+",$arr);
              }
      		$ospfv6_interface_name[$k] = "end";
		$ospfv6_interface_area[$k] = "end";
		$no_ospfv6_interface_flag = 0;
		break;
	}
	 else
		$no_ospfv6_interface_flag = 1;
}

$result= ssh2execu($Board_IP, 'ip address');
$piecesconf=explode(" ",$result);
$ip_config_success_flag = 0;
for($k = 0;$k < count($piecesconf);$k ++)
{
	if(strcmp($piecesconf[$k],$interface_ip)==0)
	{
		$ip_config_success_flag = 1;	
		break;
	}
}

?>

<h4>设备<?php echo $Board_IP; ?>的OSPF6协议配置信息:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >路由器名称</td>
		<td class="col-cell col-cell1"><?php echo $hostname; ?></td>
	</tr>
	<tr>
		<td class="col-cell" >网卡及IP</td>
		<td class="col-cell col-cell1">
		<?php
		if($ip_config_success_flag==1)
			echo "网卡:".$interface." IP:".$interface_ip;
		else
			echo "尚未配置";
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >路由ID</td>
		<td class="col-cell col-cell1"><?php echo $router_id;?></td>
	</tr>	
	<tr>
		<td class="col-cell" >路由网段及其区域号</td>
		<td class="col-cell col-cell1">
                <?php
                if($no_ospfv6_route_flag == 1)
                        echo "尚未配置路由网段";
                else
                {
                        for($k = 0;strcmp($ospfv6_area[$k],"end") != 0;$k++)
                        {
                                echo "网段:";
                                print($ospfv6_network[$k]);
                                echo "  区域号:";
                                print($ospfv6_area[$k]);
                                print("<br/>");
                        }
                }
                ?>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >已加载网卡及其使用区域号</td>
		<td class="col-cell col-cell1">
                <?php
                if($no_ospfv6_interface_flag  == 1)
                        echo "尚未加载网卡";
                else
                {
                        for($k = 0;strcmp($ospfv6_interface_name[$k],"end") != 0;$k++)
                        {
                                echo "网卡:";
                                print($ospfv6_interface_name[$k]);
                                echo "  区域号:";
                                print($ospfv6_interface_area[$k]);
                                print("<br/>");
                        }
                }
                ?>
		</td>
	</tr>	
	
</table>
</div>

<h4>OSPF6协议参数配置:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >路由器名称修改</td>
		<td class="col-cell col-cell1"><width="16" height="16">   
		<form  name="form" method="post"  action="hostname_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		<input type="text" name="hostname" id="hostname" class="txt" value="R1" style="color:#3C3C3C;"  onfocus="if(value=='R1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='R1'}" />&nbsp;
		<input  type="submit" value="修改" >
                </form>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >指定配置的网卡并输入IP及掩码</td>
		<td class="col-cell col-cell1"><width="16" height="16">   
		<form  name="form" method="post"  action="interface_ip_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		IP:<input type="text" name="ip_address" id="ip_address" class="txt" value="2001::88/96" style="color:#3C3C3C;"  onfocus="if(value=='2001::88/96'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::88/96'}" /><br>
		网卡:<input type="text" name="interface" id="interface" class="txt" size="8" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="设置" >
                </form>
		</td>
	</tr>
        <tr>
		<td class="col-cell" >修改路由id</td>
		<td class="col-cell col-cell1">
		<form  name="form" method="post"  action="router_id_change.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
                <input type="text" name="router_id" id="router_id" class="txt" value="1.1.1.3" style="color:#3C3C3C;"  onfocus="if(value=='1.1.1.3'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1.1.1.3'}"/>
		&nbsp;<input  type="submit" value="修改" >
                </form>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >添加网段及其区域号</td>
		<td class="col-cell col-cell1">  
		<form  name="form" method="post"  action="ospfv6_addroute.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		路由网段:<input type="text" name="network" id="network" class="txt" value="2111:103::/64" style="color:#3C3C3C;"  onfocus="if(value=='2111:103::/64'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2111:103::/64'}" /><br> 
       		区域号:&nbsp;&nbsp;&nbsp;<input type="text" name="area" id="area" class="txt" value="0.0.0.0" style="color:#3C3C3C;"  onfocus="if(value=='0.0.0.0'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='0.0.0.0'}" /><br> 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input  type="submit" value="添加" >
		</form>
		</td>
	</tr>	

        <tr>
		<td class="col-cell" >选择要删除的路由网段</td>
		<td class="col-cell col-cell1">
		<?php
		if($no_ospfv6_route_flag == 1)
			echo "没有可以删除的路由网段";
		else
		{
		?>
               <form name="form" method="post" action="ospfv6_deleteroute.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="ospfv6_delete">
               <?php
			for($k = 0;strcmp($ospfv6_network[$k] ,"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $ospfv6_array[$k]; ?> >  <?php echo $ospfv6_network[$k]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="删除" ></form>
		<?php
		}
		?>
		</td>
	</tr>


        <tr>
		<td class="col-cell" >激活网卡并选择其加载的区域号</td>
		<td class="col-cell col-cell1">
		<?php
		if($no_ospfv6_route_flag == 1)
			echo "没有可加载的区域号";
		else
		{
		?>
		<form  name="form" method="post"  action="ospfv6_add_interface.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		网卡:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="interface" id="interface" class="txt" value="ge1" style="color:#3C3C3C;"  onfocus="if(value=='ge1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='ge1'}" /><br> 
       		可加载区域号：<select name="ospfv6_area">
                <?php
			for($k = 0;strcmp($ospfv6_area[$k],"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $ospfv6_area[$k]; ?> > <?php echo $ospfv6_area[$k]; ?> </option>
			<?php
 			}
		?>
		</select>&nbsp;&nbsp;<input  type="submit" value="确认" ></form>
		<?php
		}
		?>
		</td>
	</tr>

        <tr>
		<td class="col-cell" >删除已激活的网卡</td>
		<td class="col-cell col-cell1"> 
		<?php
		if($no_ospfv6_interface_flag == 1)
			echo "没有可激活的网卡";
		else
		{
		?>
               <form name="form" method="post" action="ospfv6_delete_interface.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="ospfv6_delete_interface">
               <?php
			for($k = 0;strcmp($ospfv6_interface_name[$k] ,"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $ospfv6_interface[$k]; ?> >  <?php echo $ospfv6_interface_name[$k]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="删除" ></form>
		<?php
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >进程控制</td>
		<td class="col-cell col-cell1">
		<a href="../route/route_overall.php" target="frame2"><font color=black>前往路由协议进程管理</a>
		</td>
	</tr>	
</table>
</div>
<?php
}
else
{?>
		<script>window.alert('OSPF6协议进程尚未开启，请先开启进程!');</script>
	 	<script>location.href="../route/route_overall.php";</script>
<?php
}

}
else
{
?>
		<script>window.alert('无法连接IP，请先检查板卡间连通性!');</script>
	 	<script>location.href="../route/route_overall.php";</script>
<?php
}
?>

</body>
</html>


