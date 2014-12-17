<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>Nat64状态信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 600px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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

function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
    return str_replace($qian,$hou,$str);    
}

function prefix_process($my_prefix)
{
	$my_prefix = substr($my_prefix,1,-1);
	$my_prefix = trimall($my_prefix);
	for($i=-4,$temp=substr($my_prefix,$i),$count=4;substr_count($temp, '0')==$count;$count=$count+4)
	{
		$i=$i-4;
		$temp = substr($my_prefix,$i);
	}	
	$count=$count-4;
	$my_prefix = substr($my_prefix,0, strlen($my_prefix)-$count);
	$arr = str_split($my_prefix,4);
	$process_prefix = implode(":", $arr);
	return $process_prefix;
}

$Board_IP =$_GET['Board_IP'];
$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$nat64_flag = 0;
$result = ssh2execu($Board_IP, 'ps -e| grep nat64d');
if(strcmp(substr($result, -7,6),"nat64d")==0)
	$nat64_flag = 1;

if($nat64_flag == 0)
{?>
		<script>window.alert('NAT64协议进程尚未开启，请先开启进程!');</script>
	 	<script>location.href="../transition/transition_overall.php";</script>
<?php
}
if($nat64_flag == 1)
{
snmp_set_quick_print(1);
$pool_net=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.3.1.2.0");
$pool_mask=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.3.1.3.0");
$ipv6_address_temp=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.1.2.0");
$ipv4_address=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.1.1.0");
$ipv6_prefix_temp=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.2.1.2.0");
$ipv6_prefix_len=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.2.1.3.0");
$nat64AdminStatus=snmpget($Board_IP, "public",".1.3.6.1.4.1.54322.1.3.0");

$ipv6_address = prefix_process($ipv6_address_temp);
$ipv6_prefix = prefix_process($ipv6_prefix_temp);

if($nat64AdminStatus == 1)
     $showstatus = "使 能";
else
     $showstatus = "失 能";
?>

<h4>设备<?php echo $Board_IP; ?>的NAT64协议配置信息:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >地址池</td>
		<td class="col-cell  col-cell1">网段:<?php echo $pool_net; ?> 掩码:<?php echo $pool_mask; ?></td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6地址</td>
		<td class="col-cell col-cell1"><?php echo $ipv6_address; ?></td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv4地址</td>
		<td class="col-cell col-cell1"><?php echo $ipv4_address; ?></td>
	</tr>
	<tr>
		<td class="col-cell" >IPv6前缀</td>
		<td class="col-cell col-cell1">
		<?php
		if($ipv6_prefix_len == 128)
		{
			echo $ipv6_prefix;
			print("/128");
		}
		else
		{
			echo $ipv6_prefix;
			print("::/");
			echo $ipv6_prefix_len;
		}
		?>
		</td>		
	</tr>	
	<tr>
		<td class="col-cell" >使能状态</td>
		<td class="col-cell col-cell1"><?php echo $showstatus;?></td>
	</tr>	
</table>
</div>


<h4>NAT64协议参数配置:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >地址池</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="pool_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		网段:<input type="text" name="Pool_net" id="Pool_net" class="txt" size="12" value="192.168.40.0" style="color:#3C3C3C;"  onfocus="if(value=='192.168.40.0'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.40.0'}" />
       		掩码:<input type="text" name="Pool_mask" id="Pool_mask" class="txt" size="12" value="255.255.255.0" style="color:#3C3C3C;"  onfocus="if(value=='255.255.255.0'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='255.255.255.0'}" />
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6地址</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="ipv6_address_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		<input type="text" name="IPv6_address" id="IPv6_address" class="txt" value="2001:db8:1::1" style="color:#3C3C3C;"  onfocus="if(value=='2001:db8:1::1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001:db8:1::1'}" />
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv4地址</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="ipv4_address_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		<input type="text" name="IPv4_address" id="IPv4_address" class="txt" value="192.168.255.1" style="color:#3C3C3C;"  onfocus="if(value=='192.168.255.1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.255.1'}" />
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6前缀</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="ipv6_prefix_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		<input type="text" name="IPv6_prefix" id="IPv6_prefix" class="txt" value="前缀(如2001:db8:1:ffff::)" style="color:#3C3C3C;"  onfocus="if(value=='前缀(如2001:db8:1:ffff::)'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='前缀(如2001:db8:1:ffff::)'}" />/
		<input type="text" name="IPv6_prefix_len" id="IPv6_prefix_len" class="txt" size="22" value="长度:只取32,40,48,56,64,96" style="color:#3C3C3C;"  onfocus="if(value=='长度:只取32,40,48,56,64,96'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='长度:只取32,40,48,56,64,96'}" />
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >使能/失能协议</td>
		<td class="col-cell col-cell1">
                <form name="form" method="post" action="nat64_enable.php?Board_IP=<?php echo $Board_IP; ?>">
	        <select name="nat64globalstatus">
		<option value="1">使能</option>
	        <option value="2" >失能</option>
	        </select>&nbsp;
		<input  type="submit" value="设置" ></form>
              </td>
	</tr>	
	<tr>
		<td class="col-cell" >进程控制</td>
		<td class="col-cell col-cell1">
		<a href="../transition/transition_overall.php"><font color=black>前往过渡协议进程管理</a>
		</td>
	</tr>	
</table>
</div>

<?php
}
}
else
{
?>
		<script>window.alert('无法连接IP，请先检查板卡间连通性!');</script>
	 	<script>location.href="../transition/transition_overall.php";</script>
<?php
}
?>

</body>
</html>

