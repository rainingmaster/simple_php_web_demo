<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>静态路由信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width:600px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width:800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 100%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 30px;line-height: 35px;padding: 0 20px;border-bottom: 1px solid #cdcdcd;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
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
include('../include/ssh2.php');
$Board_IP =$_GET['Board_IP'];
$ini = new ini();
$hostname = $ini->iniRead("static--".$Board_IP, "hostname");
$interface = $ini->iniRead("static--".$Board_IP, "interface");
$interface_ipv4 = $ini->iniRead("static--".$Board_IP, "ipv4");
$interface_ipv6 = $ini->iniRead("static--".$Board_IP, "ipv6");

$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$result = ssh2execu($Board_IP, 'ps -e| grep rtm');
$find = 'rtm';
preg_match_all('/'.$find.'/', $result, $matches);
if(count($matches[0])==2)
{
$no_ipv4_flag = 1;
$no_ipv6_flag = 1;
snmp_set_quick_print(1);
$static_route_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.4.24.9.1.0");
$piecesconf=explode(" ",$static_route_information);
for($i = 0;$i < count($piecesconf);$i ++)
{
        if(strcmp($piecesconf[$i],"route")==0) 
        {
      		for($j=0,$k=0,$x=0,$y=0;strcmp($piecesconf[$i+$j],"route")==0;$j=$j+3,$k++)
		{
			
			$net[$k] = $piecesconf[$i+$j+1];
			$temp = $piecesconf[$i+$j+2];

			$n = strpos($temp,'i');
                	if ($n) 
                		$temp1=substr($temp,0,$n);
			else
				$temp1=$temp;

			$m = strpos($temp1,'!');
                	if ($m) 
                		$address[$k]=substr($temp1,0,$m);
			else
				$address[$k]=$temp1;


			if(strpos($net[$k],'.'))
			{
				$ipv4_net[$x] = $net[$k];
				$ipv4_address[$x] = $address[$k];
				$x++;
			}
			if(strpos($net[$k],':'))
			{
				
				$ipv6_net[$y] = $net[$k];
				$ipv6_address[$y] = $address[$k];
				$y++;
			}
                }
		if($x>0)
		{
			$ipv4_net[$x] = "end";
			$no_ipv4_flag = 0;
		}
		else	
      			$no_ipv4_flag = 1;

		if($y>0)
		{
			$ipv6_net[$y] = "end";
			$no_ipv6_flag = 0;
		}
		else	
      			$no_ipv6_flag = 1;
		break;
        }
}

$result= ssh2execu($Board_IP, 'ip address');
$piecesconf=explode(" ",$result);
$ipv4_config_success_flag = 0;
for($k = 0;$k < count($piecesconf);$k ++)
{
	if(strcmp($piecesconf[$k],$interface_ipv4)==0)
	{
		$ipv4_config_success_flag = 1;	
		break;
	}
}

$ipv6_config_success_flag = 0;
for($k = 0;$k < count($piecesconf);$k ++)
{
	if(strcmp($piecesconf[$k],$interface_ipv6)==0)
	{
		$ipv6_config_success_flag = 1;	
		break;
	}
}
?>

<h4>设备<?php echo $Board_IP; ?>的静态路由配置信息:</h4>
<div id="main">
<table class="features-table">
基本信息:
	<tr>
		<td class="col-cell" >路由器名称</td>
		<td class="col-cell col-cell1"><width="16" height="16"><?php echo $hostname; ?></td>
	</tr>
	<tr>
		<td class="col-cell" >网卡及IPv4地址</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($ipv4_config_success_flag==1)
			echo "网卡:".$interface." IPv4:".$interface_ipv4;
		else
			echo "IPv4地址未配置";
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >网卡及IPv6地址</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($ipv6_config_success_flag==1)
			echo "网卡:".$interface." IPv6:".$interface_ipv6;
		else
			echo "IPv6地址未配置";
		?>
		</td>
	</tr>
</table>
</div>

<div id="main">
<table class="features-table">
IPv4静态路由:
	<tr>
		<td class="col-cell col-cell1" >路由网段</td>
		<td class="col-cell col-cell1">路由地址</td>
	</tr>
		<?php 
		if($no_ipv4_flag == 1)
		{?>
		<tr>
		<td class="col-cell" >尚未配置</td>
		<td class="col-cell" >尚未配置</td>
		</tr>
		<?php
		}
		else
		{
			for($k = 0;strcmp($ipv4_net[$k],"end") != 0;$k++)
			{?>
			<tr>
			<td class="col-cell"><?php echo $ipv4_net[$k]; ?> </td>
			<td class="col-cell"><?php echo $ipv4_address[$k]; ?> </td>
			</tr>
		<?php
			}
		}
		?>

</table>
</div>

<div id="main">
<table class="features-table">
IPv6静态路由:
	<tr>
		<td class="col-cell col-cell1" >路由网段</td>
		<td class="col-cell col-cell1">路由地址</td>
	</tr>
		<?php 
		if($no_ipv6_flag == 1)
		{?>
		<tr>
		<td class="col-cell" >尚未配置</td>
		<td class="col-cell" >尚未配置</td>
		</tr>
		<?php
		}
		else
		{
			for($k = 0;strcmp($ipv6_net[$k],"end") != 0;$k++)
			{?>
			<tr>
			<td class="col-cell"><?php echo $ipv6_net[$k]; ?> </td>
			<td class="col-cell"><?php echo $ipv6_address[$k]; ?> </td>
			</tr>
		<?php
			}
		}
		?>

</table>
</div>

<h4>静态路由参数配置:</h4>
<div id="main1">
<table class="features-table">
	<tr>
		<td class="col-cell" >路由器名称修改</td>
		<td class="col-cell col-cell1">
		<form  name="form" method="post"  action="hostname_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		<input type="text" name="hostname" id="hostname" class="txt" value="R1" style="color:#3C3C3C;"  onfocus="if(value=='R1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='R1'}" />&nbsp;
		<input  type="submit" value="修改" >
                </form>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >指定配置的网卡并输入IPv4及掩码</td>
		<td class="col-cell col-cell1">
		<form  name="form" method="post"  action="interface_ipv4_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		IP:<input type="text" name="ipv4_address" id="ipv4_address" class="txt" value="192.168.170.154/24" style="color:#3C3C3C;"  onfocus="if(value=='192.168.170.154/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.170.154/24'}" />
		网卡:<input type="text" name="interface" id="interface" class="txt" size="10" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="设置" >
                </form>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >指定配置的网卡并输入IPv6及掩码</td>
		<td class="col-cell col-cell1">  
		<form  name="form" method="post"  action="interface_ipv6_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		IP:<input type="text" name="ipv6_address" id="ipv6_address" class="txt" value="2001::88/96" style="color:#3C3C3C;"  onfocus="if(value=='2001::88/96'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::88/96'}" />
		网卡:<input type="text" name="interface" id="interface" class="txt" size="10" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="设置" >
                </form>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >添加IPv4路由</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<form  name="form" method="post"  action="add_ipv4.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
                网段:<input type="text" name="ipv4_net" id="ipv4_net" class="txt" size="15" value="3.3.1.0/24" style="color:#3C3C3C;"  onfocus="if(value=='3.3.1.0/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='3.3.1.0/24'}" />
		地址:<input type="text" name="ipv4_address" id="ipv4_address" class="txt" size="15" value="3.3.1.1" style="color:#3C3C3C;"  onfocus="if(value=='3.3.1.1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='3.3.1.1'}" />
		&nbsp;<input  type="submit" value="添加" >
                </form>
		</td>
	</tr>	

	<tr>
		<td class="col-cell" >添加IPv6路由</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<form  name="form" method="post"  action="add_ipv6.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
                网段:<input type="text" name="ipv6_net" id="ipv6_net" class="txt" size="15" value="2001::/40" style="color:#3C3C3C;"  onfocus="if(value=='2001::/40'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::/40'}" />
		地址:<input type="text" name="ipv6_address" id="ipv6_address" class="txt" size="15" value="2001::1" style="color:#3C3C3C;"  onfocus="if(value=='2001::1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::1'}" />
		&nbsp;<input  type="submit" value="添加" >
                </form>
		</td>
	</tr>

	<tr>
		<td class="col-cell" >删除IPv4路由</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_ipv4_flag == 1)
			echo "没有可删除的路由";
		else
		{
		?>
               <form name="form" method="post" action="delete_ipv4.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="delete_ipv4_ptr">
               <?php
			for($i = 0;strcmp($ipv4_net[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $i; ?> > 网段:<?php echo $ipv4_net[$i]; ?>&nbsp;&nbsp;地址:<?php echo $ipv4_address[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="删除" ></form>
		<?php
		}
		?>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >删除IPv6路由</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_ipv6_flag == 1)
			echo "没有可删除的路由";
		else
		{
		?>
               <form name="form" method="post" action="delete_ipv6.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="delete_ipv6_ptr">
               <?php
			for($i = 0;strcmp($ipv6_net[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $i; ?> > 网段:<?php echo $ipv6_net[$i]; ?>&nbsp;&nbsp;地址:<?php echo $ipv6_address[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;
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
		<script>window.alert('静态路由协议进程尚未开启，请先开启进程!');</script>
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


