<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>BGP��Ϣ</title>
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
$router_id = "·��ID��δ����";
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

<h4>�豸<?php echo $Board_IP; ?>��BGPЭ��������Ϣ:</h4>
<div id="main">
<table class="features-table">
������Ϣ��
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
                <td class="col-cell" >·����BGP��</td>
                <td class="col-cell col-cell1"><width="16" height="16"><?php echo $bgp_id; ?></td>
        </tr>
        <tr>
                <td class="col-cell" >·��ID</td>
                <td class="col-cell col-cell1"><width="16" height="16"><?php echo $router_id; ?></td>
        </tr>
</table>
</div>

<div id="main">
<table class="features-table">
·�����μ��ھ���Ϣ��
        <tr>
                <td class="col-cell" >BGP(v4)·������:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv4_net_flag == 1)
                        echo "BGP(v4)·��������δ����";
                else
                {
                for($k = 0;strcmp($bgpv4_net_array[$k],"end") != 0;$k++)
                        echo $bgpv4_net_array[$k].'<br/>';
                }
                ?>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >BGP(v4)�ھӼ���BGP��:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv4_neighbor_flag == 1)
                        echo "�ھ���δ����";
                else
                {
                        for($k = 0;strcmp($bgpv4_neighbor_array[$k],"end") != 0;$k++)
                        {
                                echo "�ھ�:";
                                print($bgpv4_neighbor_array[$k]);
                                echo "  BGP��:";
                                print($bgpv4_neighborid_array[$k]);
                                print("<br/>");
                        }
                }
                ?>
                </td>
        </tr>
 	<tr>
                <td class="col-cell" >BGP(v6)·������:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv6_net_flag == 1)
                        echo "BGP(v6)·��������δ����";
                else
                {
                for($k = 0;strcmp($bgpv6_net_array[$k],"end") != 0;$k++)
                        echo $bgpv6_net_array[$k].'<br/>';
                }
                ?>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >BGP(v6)�ھӼ���BGP��:</td>
                <td class="col-cell col-cell1"><width="16" height="16">
                <?php
                if($no_bgpv6_neighbor_flag == 1)
                        echo "�ھ���δ����";
                else
                {
                        for($k = 0;strcmp($bgpv6_neighbor_array[$k],"end") != 0;$k++)
                        {
                                echo "�ھ�:";
                                print($bgpv6_neighbor_array[$k]);
                                echo "  BGP��:";
                                print($bgpv6_neighborid_array[$k]);
                                print("<br/>");
                        }
                }
                ?>
                </td>
        </tr>
</table>
</div>

<h4>BGPЭ�����������:</h4>
<div id="main">
<table class="features-table">
������Ϣ���ã�ע��IPv4��IPv6Ӧ������ͬһ��������
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
                <td class="col-cell" >�޸�·����BGP��</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgp_id_change.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="new_bgp_id" id="new_bgp_id" class="txt" size="24" value="ע�⣺�޸�BGP�Ž��������" style="color:#3C3C3C;"  onfocus="if(value=='ע�⣺�޸�BGP�Ž��������'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='ע�⣺�޸�BGP�Ž��������'}" />
		&nbsp;<input  type="submit" value="�޸�" >
                </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >�޸�·��ID</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="router_id_change.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="router_id" id="router_id" class="txt" value="1.1.1.3" style="color:#3C3C3C;"  onfocus="if(value=='1.1.1.3'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1.1.1.3'}" />
		&nbsp;<input  type="submit" value="�޸�" >
                </form>
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

<div id="main">
<table class="features-table">
·�����μ��ھ����ã�
        <tr>
                <td class="col-cell" >���BGP(v4)·������</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv4_add_net.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="bgpv4_net" id="bgpv4_net" class="txt" value="192.168.103.0/24" style="color:#3C3C3C;"  onfocus="if(value=='192.168.103.0/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.103.0/24'}" />
		&nbsp;<input  type="submit" value="���" >
                </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >ɾ����BGP(v4)·������</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv4_net_flag == 1)
			echo "û�п�ɾ����·������";
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
		<input  type="submit" value="ɾ��" ></form>
		<?php
		}
		?>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >���BGP(v4)�ھӼ���BGP��</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv4_add_neighbor.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                BGP�ھ�:<input type="text" name="bgpv4_neighbor" id="bgpv4_neighbor" class="txt" value="192.168.104.101" style="color:#3C3C3C;"  onfocus="if(value=='192.168.104.101'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.104.101'}" /><br>
		BGP��:&nbsp;&nbsp;<input type="text" name="bgpv4_neighborid" id="bgpv4_neighborid" class="txt" value="2" style="color:#3C3C3C;"  onfocus="if(value=='2'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2'}" /><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="submit" value="���" >
               </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >ɾ��BGP(v4)�ھ�</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv4_neighbor_flag == 1)
			echo "û�п�ɾ�����ھ�";
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
		<input  type="submit" value="ɾ��" ></form>
		<?php
		}
		?>
                </td>
        </tr>
   	<tr>
                <td class="col-cell" >���BGP(v6)·������</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv6_add_net.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                <input type="text" name="bgpv6_net" id="bgpv6_net" class="txt"  value="2111:103::/64" style="color:#3C3C3C;"  onfocus="if(value=='2111:103::/64'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2111:103::/64'}" />
		&nbsp;<input  type="submit" value="���" >
                </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >ɾ��BGP(v6)·������</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv6_net_flag == 1)
			echo "û�п���ɾ����·������";
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
		<input  type="submit" value="ɾ��" ></form>
		<?php
		}
		?>
                </td>
        </tr>
 	<tr>
                <td class="col-cell" >���BGP(v6)�ھӼ���BGP��</td>
                <td class="col-cell col-cell1">
                <form  name="form" method="post"  action="bgpv6_add_neighbor.php?Board_IP=<?php echo $Board_IP; ?>&bgp_id=<?php echo $bgp_id; ?>" onSubmit="return checksignup()">
                BGP�ھӣ�<input type="text" name="bgpv6_neighbor" id="bgpv6_neighbor" class="txt"  value="2111:107::113" style="color:#3C3C3C;"  onfocus="if(value=='2111:107::113'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2111:107::113'}" /><br>
		BGP�ţ�&nbsp;&nbsp;<input type="text" name="bgpv6_neighborid" id="bgpv6_neighborid" class="txt"  value="1" style="color:#3C3C3C;"  onfocus="if(value=='1'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='1'}" /><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="submit" value="���" >
               </form>
                </td>
        </tr>
        <tr>
                <td class="col-cell" >ɾ��BGP(v6)�ھ�</td>
                <td class="col-cell col-cell1">
		<?php
		if($no_bgpv6_neighbor_flag == 1)
			echo "û�п�ɾ�����ھ�";
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
		<input  type="submit" value="ɾ��" ></form>
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
		<script>window.alert('BGPЭ�������δ���������ȿ�������!');</script>
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


