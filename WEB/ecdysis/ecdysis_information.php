<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>ECDYSIS</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 450px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$ecdysis_flag = 0;
$result = ssh2execu($Board_IP, 'ps -e| grep ecdysisd');
if(strcmp(substr($result, -9,8),"ecdysisd")==0)
	$ecdysis_flag = 1;

if($ecdysis_flag == 0)
{?>
		<script>window.alert('ECDYSIS协议进程尚未开启，请先开启进程!');</script>
	 	<script>location.href="../transition/transition_overall.php";</script>
<?php
}
if($ecdysis_flag == 1)
{
snmp_set_quick_print(1);
snmpset($Board_IP,"public",".1.3.6.1.2.1.321.1.1.0","i","1");
$IPv4Address=snmpget($Board_IP, "public",".1.3.6.1.2.1.321.2.1.0");
$PrefixAddress=snmpget($Board_IP, "public",".1.3.6.1.2.1.321.2.2.0");
$ecdysisAdminStatus=snmpget($Board_IP, "public",".1.3.6.1.2.1.321.1.2.0");
$piecesconf=explode(" ",$PrefixAddress);

if( strcmp($IPv4Address,"0.0.0.0")==0 )
	$IPv4Address = "尚未配置";
if( count($piecesconf) == 5 )
	$PrefixAddress = "尚未配置";

if($ecdysisAdminStatus == 1)
     $showstatus = "使 能";
else
     $showstatus = "失 能";
?>
<h4>设备<?php echo $Board_IP; ?>的ECDYSIS协议配置信息:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >IPv4地址</td>
		<td class="col-cell  col-cell1"><width="16" height="16">&nbsp;<?php echo $IPv4Address; ?>&nbsp;</td>		
	</tr>
	<tr>
		<td class="col-cell" >前缀地址</td>
		<td class="col-cell  col-cell1"><width="16" height="16">&nbsp;<?php echo substr($PrefixAddress,1,-1); ?>&nbsp;</td>		
	</tr>
	<tr>
		<td class="col-cell" >使能状态</td>
		<td class="col-cell  col-cell1"><width="16" height="16">&nbsp;<?php echo $showstatus; ?>&nbsp;</td>	
	</tr>	
</table>
</div>

<h4>ECDYSIS协议参数配置:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >IPv4与前缀设置</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<form  name="form" method="post"  action="gateway_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
		IPv4地址:<input type="text" name="IPv4Address" id="IPv4Address" class="txt" value="192.168.1.9" style="color:#3C3C3C;"  onfocus="if(value=='192.168.1.9'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.1.9'}" /><br>
       		前缀地址:<input type="text" name="PrefixAddress" id="PrefixAddress" class="txt"value="2001::/64" style="color:#3C3C3C;"  onfocus="if(value=='2001::/64'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::/64'}"  /><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="submit" value="设置" >
		</form>
                </td>
	</tr>
	<tr>
		<td class="col-cell" >使能/失能协议</td>
		<td class="col-cell col-cell1"><width="16" height="16">
                <form name="form" method="post" action="enable_gateway.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	        <select name="gatewayglobalstatus">
                <option value="1">使能</option>
	        <option value="2">失能</option>
	        </select>&nbsp;&nbsp;
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

