<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>BGP信息</title>
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
$hostname = $ini->iniRead("bgp--".$Board_IP, "hostname");
$interface = $ini->iniRead("bgp--".$Board_IP, "interface");
$interface_ipv4 = $ini->iniRead("bgp--".$Board_IP, "ipv4");
$interface_ipv6 = $ini->iniRead("bgp--".$Board_IP, "ipv6");

$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$result = ssh2execu($Board_IP, 'ps -e| grep bgpd');
if(strcmp(substr($result, -5,4),"bgpd")==0)
{
$no_bgpv4_net_flag = 1;
$no_bgpv4_neighbor_flag = 1;
$router_id = "路由ID尚未配置";
snmp_set_quick_print(1);
$bgpv4_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.15.9.1.0");
$piecesconf=explode(" ",$bgpv4_information);

for($i = 0;$i < count($piecesconf);$i++)
{
	if(strcmp($piecesconf[$i],"bgp")==0 && strcmp($piecesconf[$i+1],"router-id")!=0)
	{
		$bgp_id = $piecesconf[$i+1];
		break;
	}	
}

for($i = 0;$i < count($piecesconf);$i++)
{
        if(strcmp($piecesconf[$i],"router-id")==0)
	{
        	$temp = $piecesconf[$i+1];
                $n = strpos($temp,'!');
                if ($n)
                	$router_id=substr($temp,0,$n);
                else
                        $router_id=$temp;
	}
     

        if(strcmp($piecesconf[$i],"network")==0)
        {
                for($j = 0,$k=0;strcmp($piecesconf[$i+$j],"network")==0;$j=$j+2)
                {
			if( eregi(".", $piecesconf[$i+$j+1]) && !eregi(":", $piecesconf[$i+$j+1]) )
                        {
                                $temp = $piecesconf[$i+$j+1];
                                $n = strpos($temp,'!');
                                if ($n)
                                        $bgpv4_net_array[$k]=substr($temp,0,$n);
                                else
                                        $bgpv4_net_array[$k]=$temp;
                                $k = $k + 1;
				$no_bgpv4_net_flag = 0;
                        }
              }
                $bgpv4_net_array[$k]= "end";
                break;
        }
         else
                $no_bgpv4_net_flag = 1;
}

for($i = 0;$i < count($piecesconf);$i++)
{
        if(strcmp($piecesconf[$i],"neighbor")==0 && strcmp($piecesconf[$i+2],"remote-as")==0)
        {
                for($j = 0,$k=0;strcmp($piecesconf[$i+$j],"neighbor")==0;$j=$j+4)
                {
                       if ( eregi(".", $piecesconf[$i+$j+1]) && !eregi(":", $piecesconf[$i+$j+1]) )
                        {
                                $bgpv4_neighbor_array[$k] = $piecesconf[$i+$j+1];
                                $temp = $piecesconf[$i+$j+3];
                                $n = strpos($temp,'!');
                                if ($n)
                                        $bgpv4_neighborid_array[$k]=substr($temp,0,$n);
                                else
                                        $bgpv4_neighborid_array[$k]=$temp;
                                $k = $k + 1;
	 			$no_bgpv4_neighbor_flag = 0;
                        }
              }
                $bgpv4_neighbor_array[$k]  = "end";
                $bgpv4_neighborid_array[$k]= "end";
                break;
        }
         else
                $no_bgpv4_neighbor_flag = 1;
}

if( $no_bgpv4_neighbor_flag == 0 )
{
	for($k = 0;strcmp($bgpv4_neighbor_array[$k],"end") != 0; $k++)	
	{
		$arr = array($bgpv4_neighbor_array[$k],$bgpv4_neighborid_array[$k]);
		$bgpv4_neighbor[$k]=implode("+",$arr);
	}
	$bgpv4_neighbor[$k] = "end";
}

$no_bgpv6_net_flag = 1;
$no_bgpv6_neighbor_flag = 1;
snmp_set_quick_print(1);
$bgpv6_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.15.9.1.0");
$piecesconf=explode(" ",$bgpv6_information);

for($i = 0,$k=0;$i < count($piecesconf);$i++)
{
	if(strpos($piecesconf[$i],":") && strpos($piecesconf[$i],"/"))
        {
			$bgpv6_net_array[$k]= $piecesconf[$i];
                        $n = strpos($temp,'!');
                       	$k = $k + 1;
			$no_bgpv6_net_flag = 0;
                	$bgpv6_net_array[$k]= "end";
		
        }
}

for($i = 0;$i < count($piecesconf);$i++)
{
        if(strcmp($piecesconf[$i],"neighbor")==0 && strcmp($piecesconf[$i+2],"remote-as")==0)
        {
                for($j = 0,$k=0;strcmp($piecesconf[$i+$j],"neighbor")==0;$j=$j+4)
                {
			$test=explode(":",$piecesconf[$i+$j+1]);
			if(strlen($test[0])==4)
			{
                                $bgpv6_neighbor_array[$k] = $piecesconf[$i+$j+1];
                                $temp = $piecesconf[$i+$j+3];
                                $n = strpos($temp,'!');
                                if ($n)
                                        $bgpv6_neighborid_array[$k]=substr($temp,0,$n);
                                else
                                        $bgpv6_neighborid_array[$k]=$temp;
                                $k = $k + 1;
	 			$no_bgpv6_neighbor_flag = 0;
			}
                        
              }
                $bgpv6_neighbor_array[$k]  = "end";
                $bgpv6_neighborid_array[$k]= "end";
                break;
        }
         else
                $no_bgpv6_neighbor_flag = 1;
}

if( $no_bgpv6_neighbor_flag == 0 )
{
	for($k = 0;strcmp($bgpv6_neighbor_array[$k],"end") != 0; $k++)	
	{
		$arr = array($bgpv6_neighbor_array[$k],$bgpv6_neighborid_array[$k]);
		$bgpv6_neighbor[$k]=implode("+",$arr);
	}
	$bgpv6_neighbor[$k] = "end";
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

<h4>设备<?php echo $Board_IP; ?>的BGP协议配置信息:</h4>
<div id="main">
<table class="features-table">
基本信息：
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
        <tr>
                <td class="col-cell" >路由器BGP号</td>
                <td class="col-cell col-cell1"><width="16" height="16"><?php echo $bgp_id; ?></td>
        </tr>
        <tr>
                <td class="col-cell" >路由ID</td>
                <td class="col-cell col-cell1"><width="16" height="16"><?php echo $router_id; ?></td>
        </tr>
</table>
</div>

<div id="main">
<table class="features-table">
路由网段及邻居信息：
        <tr>
                <td class="col-cell" >BGP(v4)路由网段:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv4_net_flag == 1)
                        echo "BGP(v4)路由网段尚未配置";
                else
                {
                for($k = 0;strcmp($bgpv4_net_array[$k],"end") != 0;$k++)
                        echo $bgpv4_net_array[$k].'<br/>';
                }
                ?>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >BGP(v4)邻居及其BGP号:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv4_neighbor_flag == 1)
                        echo "邻居尚未配置";
                else
                {
                        for($k = 0;strcmp($bgpv4_neighbor_array[$k],"end") != 0;$k++)
                        {
                                echo "邻居:";
                                print($bgpv4_neighbor_array[$k]);
                                echo "  BGP号:";
                                print($bgpv4_neighborid_array[$k]);
                                print("<br/>");
                        }
                }
                ?>
                </td>
        </tr>
 	<tr>
                <td class="col-cell" >BGP(v6)路由网段:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv6_net_flag == 1)
                        echo "BGP(v6)路由网段尚未配置";
                else
                {
                for($k = 0;strcmp($bgpv6_net_array[$k],"end") != 0;$k++)
                        echo $bgpv6_net_array[$k].'<br/>';
                }
                ?>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >BGP(v6)邻居及其BGP号:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv6_neighbor_flag == 1)
                        echo "邻居尚未配置";
                else
                {
                        for($k = 0;strcmp($bgpv6_neighbor_array[$k],"end") != 0;$k++)
                        {
                                echo "邻居:";
                                print($bgpv6_neighbor_array[$k]);
                                echo "  BGP号:";
                                print($bgpv6_neighborid_array[$k]);
                                print("<br/>");
                        }
                }
                ?>
                </td>
        </tr>
</table>
</div>

<h4>BGP协议参数段配置:</h4>
<div id="main">
<table class="features-table">
基本信息设置（注意IPv4及IPv6应配置于同一网卡）：
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
  		IP:<input type="text" name="ipv4_address" id="ipv4_address" class="txt" value="192.168.170.154/24" style="color:#3C3C3C;"  onfocus="if(value=='192.168.170.154/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.170.154/24'}" /><br>
		网卡:<input type="text" name="interface" id="interface" class="txt" size="10" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="设置" >
                </form>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >指定配置的网卡并输入IPv6及掩码</td>
		<td class="col-cell col-cell1">  
		<form  name="form" method="post"  action="interface_ipv6_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		IP:<input type="text" name="ipv6_address" id="ipv6_address" class="txt" value="2001::88/96" style="color:#3C3C3C;"  onfocus="if(value=='2001::88/96'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::88/96'}" /><br>
		网卡:<input type="text" name="interface" id="interface" class="txt" size="10" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="设置" >
                </form>
		</td>
	</tr>	
        <tr>
                <td class="col-cell" >修改路由器BGP号</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgp_id_change.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="new_bgp_id" id="new_bgp_id" class="txt" size="24" value="注意：修改BGP号将清空配置" style="color:#3C3C3C;"  onfocus="if(value=='注意：修改BGP号将清空配置'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='注意：修改BGP号将清空配置'}" />
		&nbsp;<input  type="submit" value="修改" >
                </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >修改路由ID</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="router_id_change.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="router_id" id="router_id" class="txt" value="1.1.1.3" style="color:#3C3C3C;"  onfocus="if(value=='1.1.1.3'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1.1.1.3'}" />
		&nbsp;<input  type="submit" value="修改" >
                </form>
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

<div id="main">
<table class="features-table">
路由网段及邻居设置：
        <tr>
                <td class="col-cell" >添加BGP(v4)路由网段</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv4_add_net.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="bgpv4_net" id="bgpv4_net" class="txt" value="192.168.103.0/24" style="color:#3C3C3C;"  onfocus="if(value=='192.168.103.0/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.103.0/24'}" />
		&nbsp;<input  type="submit" value="添加" >
                </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >删除的BGP(v4)路由网段</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv4_net_flag == 1)
			echo "没有可删除的路由网段";
		else
		{
		?>
              <form name="form" method="post" action="bgpv4_delete_net.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" >&nbsp;
	       <select name="bgpv4_delete_net">
              <?php
			for($k = 0;strcmp($bgpv4_net_array[$k] ,"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $bgpv4_net_array[$k]; ?> >  <?php echo $bgpv4_net_array[$k]; ?> </option>
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
                <td class="col-cell" >添加BGP(v4)邻居及其BGP号</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv4_add_neighbor.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                BGP邻居:<input type="text" name="bgpv4_neighbor" id="bgpv4_neighbor" class="txt" value="192.168.104.101" style="color:#3C3C3C;"  onfocus="if(value=='192.168.104.101'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.104.101'}" /><br>
		BGP号:&nbsp;&nbsp;<input type="text" name="bgpv4_neighborid" id="bgpv4_neighborid" class="txt" value="2" style="color:#3C3C3C;"  onfocus="if(value=='2'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2'}" /><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="submit" value="添加" >
               </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >删除BGP(v4)邻居</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv4_neighbor_flag == 1)
			echo "没有可删除的邻居";
		else
		{
		?>
              <form name="form" method="post" action="bgpv4_delete_neighbor.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" >&nbsp;
	       <select name="bgpv4_delete_neighbor">
              <?php
			for($k = 0;strcmp($bgpv4_neighbor[$k],"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $bgpv4_neighbor[$k]; ?> >  <?php echo $bgpv4_neighbor_array[$k]; ?> </option>
			<?php
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
                <td class="col-cell" >添加BGP(v6)路由网段</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv6_add_net.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="bgpv6_net" id="bgpv6_net" class="txt"  value="2111:103::/64" style="color:#3C3C3C;"  onfocus="if(value=='2111:103::/64'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2111:103::/64'}" />
		&nbsp;<input  type="submit" value="添加" >
                </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >删除BGP(v6)路由网段</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv6_net_flag == 1)
			echo "没有可以删除的路由网段";
		else
		{
		?>
              <form name="form" method="post" action="bgpv6_delete_net.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" >&nbsp;
	       <select name="bgpv6_delete_net">
              <?php
			for($k = 0;strcmp($bgpv6_net_array[$k] ,"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $bgpv6_net_array[$k]; ?> >  <?php echo $bgpv6_net_array[$k]; ?> </option>
			<?php
 			}
		?>
	       </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="删除" ></form>
		<?php
		}
		?>
                </td>
        </tr>
 	<tr>
                <td class="col-cell" >添加BGP(v6)邻居及其BGP号</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv6_add_neighbor.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                BGP邻居：<input type="text" name="bgpv6_neighbor" id="bgpv6_neighbor" class="txt"  value="2111:107::113" style="color:#3C3C3C;"  onfocus="if(value=='2111:107::113'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2111:107::113'}" /><br>
		BGP号：&nbsp;&nbsp;<input type="text" name="bgpv6_neighborid" id="bgpv6_neighborid" class="txt"  value="1" style="color:#3C3C3C;"  onfocus="if(value=='1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1'}" /><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="submit" value="添加" >
               </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >删除BGP(v6)邻居</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv6_neighbor_flag == 1)
			echo "没有可删除的邻居";
		else
		{
		?>
              <form name="form" method="post" action="bgpv6_delete_neighbor.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" >&nbsp;
	       <select name="bgpv6_delete_neighbor">
              <?php
			for($k = 0;strcmp($bgpv6_neighbor[$k],"end") != 0;$k++)
			{
			?>
				<option value= <?php echo $bgpv6_neighbor[$k]; ?> >  <?php echo $bgpv6_neighbor_array[$k]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="删除" ></form>
		<?php
		}
		?>
                </td>
        </tr>
</table>
</div>
<?php
}
else
{?>
		<script>window.alert('BGP协议进程尚未开启，请先开启进程!');</script>
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


