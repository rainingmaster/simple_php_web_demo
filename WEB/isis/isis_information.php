<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>ISIS��Ϣ</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width:650px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
$hostname = $ini->iniRead("isis--".$Board_IP, "hostname");
$interface = $ini->iniRead("isis--".$Board_IP, "interface");
$interface_ipv4 = $ini->iniRead("isis--".$Board_IP, "ipv4");
$interface_ipv6 = $ini->iniRead("isis--".$Board_IP, "ipv6");

$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$result = ssh2execu($Board_IP, 'ps -e| grep isisd');
if(strcmp(substr($result, -6,5),"isisd")==0)
{
$no_isisv4_interface_flag = 1;
$net = "net����δ����";
snmp_set_quick_print(1);
$isisv4_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.316.1.1.0");
$piecesconf=explode(" ",$isisv4_information);
for($i=0,$k=0;$i < count($piecesconf);$i ++)
{
	if(strcmp($piecesconf[$i],"net")==0)
        	$net = $piecesconf[$i+1];
    
	if(strcmp($piecesconf[$i],"ip")==0 && strcmp($piecesconf[$i+1],"router")==0) 
	{
		$isisv4_interface[$k] = $piecesconf[$i-1];
		$isisv4_id[$k] = substr($piecesconf[$i+3],0,1);
		if(strcmp(substr($piecesconf[$i+3],1,2),"!") != 0)
			$isisv4_id[$k] = substr($piecesconf[$i+3],0,2);
		$arr = array($isisv4_id[$k],$isisv4_interface[$k],);
		$isisv4_interface_and_id[$k] = implode("+",$arr);
		$k = $k + 1;
		$no_isisv4_interface_flag = 0;
		
	}
}
$isisv4_interface[$k] = "end";
if($k == 0)
	$no_isisv4_interface_flag = 1;

$no_isisv6_interface_flag = 1;
$isisv6_information=snmpget($Board_IP, "public",".1.3.6.1.2.1.316.1.1.0");
$piecesconf=explode(" ",$isisv6_information);
for($i=0,$k=0;$i < count($piecesconf);$i ++)
{    
	if(strcmp($piecesconf[$i],"ipv6")==0 && strcmp($piecesconf[$i+1],"router")==0) 
	{
		$isisv6_interface[$k] = $piecesconf[$i-1];
		if(strcmp($piecesconf[$i-2],"isis") == 0)
			$isisv6_interface[$k] = $piecesconf[$i-5];

		$isisv6_id[$k] = substr($piecesconf[$i+3],0,1);
		if(strcmp(substr($piecesconf[$i+3],1,2),"!") != 0)
			$isisv6_id[$k] = substr($piecesconf[$i+3],0,2);
		$arr = array($isisv6_id[$k],$isisv6_interface[$k]);
		$isisv6_interface_and_id[$k] = implode("+",$arr);
		$k = $k + 1;
		$no_isisv6_interface_flag = 0;
		
	}
}
$isisv6_interface[$k] = "end";
if($k == 0)
	$no_isisv6_interface_flag = 1;

$no_router_isis_flag = 1;
for($i=0,$k=0;$i < count($piecesconf);$i ++)
{    
	if(strcmp($piecesconf[$i],"isis")==0) 
	{
		if(strcmp($piecesconf[$i-1],"router")!=0 && strcmp($piecesconf[$i+1],"circuit-type")!=0)
		{
			$router_isis[$k] = $piecesconf[$i+1];
			for($m=1;$m<8;$m++)
			{
				if(strcmp($piecesconf[$i+$m],"isis")!=0)
				{
					if(strcmp($piecesconf[$i+$m],"is-type")==0)
					{
						if(strpos($piecesconf[$i+$m+1],"-1"))
							$router_isis_type[$k] = "level-1";

						if(strpos($piecesconf[$i+$m+1],"-2"))
							$router_isis_type[$k] = "level-2-only";
						break;
					}	
					else
						$router_isis_type[$k] = "level-1-2";
				}
				else
				{
					$router_isis_type[$k] = "level-1-2";
					break;
				}
			}
			$k = $k + 1;
			$no_router_isis_flag = 0;
		}
	}
}
$router_isis[$k] = "end";
if($k == 0)
	$no_router_isis_flag = 1;


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

<h4>�豸<?php echo $Board_IP; ?>��ISISЭ��������Ϣ:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >·��������</td>
		<td class="col-cell col-cell1"><width="16" height="16"><?php echo $hostname; ?></td>
	</tr>
	<tr>
		<td class="col-cell" >������IPv4��ַ</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($ipv4_config_success_flag==1)
			echo "����:".$interface." IPv4:".$interface_ipv4;
		else
			echo "IPv4��ַδ����";
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >������IPv6��ַ</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($ipv6_config_success_flag==1)
			echo "����:".$interface." IPv6:".$interface_ipv6;
		else
			echo "IPv6��ַδ����";
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >net��</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php 
		if($no_router_isis_flag == 1)
			echo "����δ����ISIS�ţ��޷���ȡnet��";
		else
			echo $net; 
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >ISIS���̺ż�������</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
                if($no_router_isis_flag == 1)
                        echo "δ����ISIS���̺�";
                else
                {
                        for($k = 0;strcmp($router_isis[$k],"end") != 0;$k++)
                        {
                                print("���̺�:");
				print($router_isis[$k]);
				print("����:");
				print($router_isis_type[$k]);
				print("<br>");
                        }
                }
                ?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >ʹ��ISIS(v4)Э������ڼ�����̺�</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
                if($no_isisv4_interface_flag == 1)
                        echo "û��ʹ��ISISЭ�������";
                else
                {
                        for($k = 0;strcmp($isisv4_interface[$k],"end") != 0;$k++)
                        {
                                print("����:");
				print($isisv4_interface[$k]);
				print("���̺�:");
				print($isisv4_id[$k]);
				print("<br>");
                        }
                }
                ?>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >ʹ��ISIS(v6)Э������ڼ�����̺�</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
                if($no_isisv6_interface_flag == 1)
                        echo "û��ʹ��ISISЭ�������";
                else
                {
                        for($k = 0;strcmp($isisv6_interface[$k],"end") != 0;$k++)
                        {
                                print("����:");
				print($isisv6_interface[$k]);
				print("���̺�:");
				print($isisv6_id[$k]);
				print("<br>");
                        }
                }
                ?>
		</td>
	</tr>	
</table>
</div>

<h4>ISISЭ���������:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >·���������޸�</td>
		<td class="col-cell col-cell1">
		<form  name="form" method="post"  action="hostname_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		<input type="text" name="hostname" id="hostname" class="txt" value="R1" style="color:#3C3C3C;"  onfocus="if(value=='R1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='R1'}" />&nbsp;
		<input  type="submit" value="�޸�" >
                </form>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >ָ�����õ�����������IPv4������</td>
		<td class="col-cell col-cell1">
		<form  name="form" method="post"  action="interface_ipv4_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		IP:<input type="text" name="ipv4_address" id="ipv4_address" class="txt" value="192.168.170.154/24" style="color:#3C3C3C;"  onfocus="if(value=='192.168.170.154/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.170.154/24'}" /><br>
		����:<input type="text" name="interface" id="interface" class="txt" size="10" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="����" >
                </form>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >ָ�����õ�����������IPv6������</td>
		<td class="col-cell col-cell1">  
		<form  name="form" method="post"  action="interface_ipv6_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
  		IP:<input type="text" name="ipv6_address" id="ipv6_address" class="txt" value="2001::88/96" style="color:#3C3C3C;"  onfocus="if(value=='2001::88/96'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001::88/96'}" /><br>
		����:<input type="text" name="interface" id="interface" class="txt" size="10" value="eth5" style="color:#3C3C3C;"  onfocus="if(value=='eth5'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth5'}" />&nbsp;
		<input  type="submit" value="����" >
                </form>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >�޸�net��</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag == 1)
			echo "������ӵ�һ��ISIS���̺�ʱ����net��";
		else
		{
		?>
		<form  name="form" method="post"  action="net_id_change.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
                <input type="text" name="net_id" id="net_id" class="txt" size="25" value="49.0001.0000.0000.0003.00" style="color:#3C3C3C;"  onfocus="if(value=='49.0001.0000.0000.0003.00'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='49.0001.0000.0000.0003.00'}"/>
		&nbsp;<input  type="submit" value="�޸�" >
                </form>
		<?php
		}
		?>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >���ISIS���̺Ų���������</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag == 1)
		{
		?>
		<form  name="form" method="post"  action="add_router_isis1.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		���̺�:<input type="text" name="router_isis" id="router_isis" class="txt" size="4" value="1~99" style="color:#3C3C3C;"  onfocus="if(value=='1~99'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1~99'}" />
		����:	
		 <select name="router_isis_type">               
		<option value=1>level-1</option>
		<option value=2>level-1-2</option>
		<option value=3>level-2-only</option>
	        </select><br>
 		net��:<input type="text" name="net_id" id="net_id" class="txt" size="25" value="49.0001.0000.0000.0003.00" style="color:#3C3C3C;"  onfocus="if(value=='49.0001.0000.0000.0003.00'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='49.0001.0000.0000.0003.00'}"/>
		<input  type="submit" value="���">
		</form>
		<?php
		}
		else
		{
		?>
		<form  name="form" method="post"  action="add_router_isis.php?Board_IP=<?php echo $Board_IP; ?>&net=<?php echo $net; ?>" onSubmit="return checksignup()">
       		���̺�:<input type="text" name="router_isis" id="router_isis" class="txt" size="4" value="1~99" style="color:#3C3C3C;"  onfocus="if(value=='1~99'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1~99'}" />
		����:	
		 <select name="router_isis_type">               
		<option value=1>level-1</option>
		<option value=2>level-1-2</option>
		<option value=3>level-2-only</option>
	        </select>
		<input  type="submit" value="���">
		</form>
		<?php
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >���ʹ��ISIS(v4)�����ڼ�����̺�</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag == 1)
			echo "�޷���ӣ��������ISIS���̺�";
		else
		{
		?>
		<form  name="form" method="post"  action="isisv4_add_interface.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		����:<input type="text" name="network" id="network" class="txt" size="4" value="eth1" style="color:#3C3C3C;"  onfocus="if(value=='eth1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth1'}" />
		���̺�:	
		 <select name="isisv4_id">               
		<?php
			for($i = 0;strcmp($router_isis[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $router_isis[$i]; ?> >  <?php echo $router_isis[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>
		<input  type="submit" value="���">
		</form>
		<?php
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >���ʹ��ISIS(v6)�����ڼ�����̺�</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag == 1)
			echo "�޷���ӣ��������ISIS���̺�";
		else
		{
		?>
		<form  name="form" method="post"  action="isisv6_add_interface.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
       		����:<input type="text" name="network" id="network" class="txt" size="4" value="eth2" style="color:#3C3C3C;"  onfocus="if(value=='eth2'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='eth2'}" />
		���̺�:
		 <select name="isisv6_id">               
		<?php
			for($i = 0;strcmp($router_isis[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $router_isis[$i]; ?> >  <?php echo $router_isis[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>
		<input  type="submit" value="���">
		</form>
		<?php
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >ɾ��ISIS���̺�</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag == 1)
			echo "û�п�ɾ����ISIS���̺�";
		else
		{
		?>
               <form name="form" method="post" action="delete_router_isis.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="delete_router_isis">
               <?php
			for($i = 0;strcmp($router_isis[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $router_isis[$i]; ?> >  <?php echo $router_isis[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="ɾ��" ></form>
		<?php
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >ɾ��ʹ��ISIS(v4)������</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag==1 || $no_isisv4_interface_flag==1)
			echo "û�п�ɾ��������";
		else
		{
		?>
               <form name="form" method="post" action="isisv4_delete_interface.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="isisv4_delete_interface_ptr">
               <?php
			for($i = 0;strcmp($isisv4_interface[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $i; ?> >  <?php echo $isisv4_interface[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="ɾ��" ></form>
		<?php
		}
		?>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >ɾ��ʹ��ISIS(v6)������</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<?php
		if($no_router_isis_flag == 1 || $no_isisv6_interface_flag==1)
			echo "û�п�ɾ��������";
		else
		{
		?>
               <form name="form" method="post" action="isisv6_delete_interface.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	       <select name="isisv6_delete_interface_ptr">
               <?php
			for($i = 0;strcmp($isisv6_interface[$i] ,"end") != 0;$i++)
			{
			?>
				<option value= <?php echo $i; ?> >  <?php echo $isisv6_interface[$i]; ?> </option>
			<?php
 			}
		?>
	        </select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="ɾ��" ></form>
		<?php
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="col-cell" >���̿���</td>
		<td class="col-cell col-cell1">
		<a href="../route/route_overall.php" target="frame2"><font color=black>ǰ��·��Э����̹���</a>
		</td>
	</tr>	
</table>
</div>
<?php
}
else
{?>
		<script>window.alert('ISISЭ�������δ���������ȿ�������!');</script>
	 	<script>location.href="../route/route_overall.php";</script>
<?php
}

}
else
{
?>
		<script>window.alert('�޷�����IP�����ȼ��忨����ͨ��!');</script>
	 	<script>location.href="../route/route_overall.php";</script>
<?php
}
?>
</body>
</html>


