<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>Nat-PT状态信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 500px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 450px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
$natpt_flag = 0;
$result1 = ssh2execu($Board_IP, 'ps -e| grep natptd');
if(strcmp(substr($result1, -7,6),"natptd")==0)
	$natpt_flag = 1;

if($natpt_flag == 0)
{?>
		<script>window.alert('Nat-PT协议进程尚未开启，请先开启进程!');</script>
	 	<script>location.href="../transition/transition_overall.php";</script>
<?php
}
if($natpt_flag == 1)
{
snmp_set_quick_print(1);
$pool_start=snmpget($Board_IP, "public",".1.3.6.1.4.1.54323.1.1.0");
$pool_end=snmpget($Board_IP, "public",".1.3.6.1.4.1.54323.1.2.0");
$prefix_temp=snmpget($Board_IP, "public",".1.3.6.1.4.1.54323.1.3.0");
$dns=snmpget($Board_IP, "public",".1.3.6.1.4.1.54323.1.4.0");
$natptAdminStatus=snmpget($Board_IP, "public",".1.3.6.1.4.1.54323.1.5.0");
$prefix = prefix_process($prefix_temp);
if($natptAdminStatus == 1)
     $showstatus = "使 能";
else
     $showstatus = "失 能";
?>
<h4>设备<?php echo $Board_IP; ?>的Nat-PT协议配置信息:</h4>
<div id="main1">
<table class="features-table">
	<tr>
		<td class="col-cell" >地址池</td>
		<td class="col-cell  col-cell1"><?php echo $pool_start; ?>~<?php echo $pool_end; ?></td>		
	</tr>
	<tr>
		<td class="col-cell" >转换前缀</td>
		<td class="col-cell col-cell1"><?php echo $prefix; ?>::</td>		
	</tr>
	<tr>
		<td class="col-cell" >DNS</td>
		<td class="col-cell col-cell1"><?php echo $dns; ?></td>
	</tr>	
	<tr>
		<td class="col-cell" >使能状态</td>
		<td class="col-cell col-cell1"><?php echo $showstatus;?></td>
	</tr>	
</table>
</div>


<h4>Nat-PT协议参数配置:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >地址池</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="pool_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		<input type="text" name="Pool_start" id="Pool_start" class="txt" size="12" value="192.168.254.1" style="color:#3C3C3C;"  onfocus="if(value=='192.168.254.1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.254.1'}" /> ~
       		<input type="text" name="Pool_end" id="Pool_end" class="txt" size="12" value="192.168.254.10" style="color:#3C3C3C;"  onfocus="if(value=='192.168.254.10'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.254.10'}" />
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >转换前缀</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="prefix_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		<input type="text" name="IPv6Prefix" id="IPv6Prefix" class="txt" value="2001:ffff::" style="color:#3C3C3C;"  onfocus="if(value=='2001:ffff::'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001:ffff::'}" />
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >DNS</td>
		<td class="col-cell  col-cell1">
		<form  name="form" method="post"  action="dns_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		<input type="text" name="DNS_address" id="DNS_address" class="txt" value="114.114.114.114" style="color:#3C3C3C;"  onfocus="if(value=='114.114.114.114'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='114.114.114.114'}"/>
		<input  type="submit" value="设置" >
		</form>
		</td>		
	</tr>
	<tr>
		<td class="col-cell" >使能/失能协议</td>
		<td class="col-cell col-cell1">
                <form name="form" method="post" action="natpt_enable.php?Board_IP=<?php echo $Board_IP; ?>">
	        <select name="natptglobalstatus">
		<option value="1">使能</option>
	        <option value="2" >失能</option>
	        </select>&nbsp;
		<input  type="submit" value="确认" ></form>
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

