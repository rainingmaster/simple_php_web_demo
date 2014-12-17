<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>DS-Lite״̬��Ϣ</title>
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

function cmd_get_val($content, $command)
{
	$cmd_type = strpos($command, '?');
	if(!$cmd_type)//������?��ƥ��--Ӧû�е�һ������?������
		if(strpos($content, $command))
			return true;
		else
			return false;
			
	/*����?��ƥ��*/
	$ar_ret = array();//��������
	$keyword = substr($command, 0, $cmd_type);//����ƥ��Ĺؼ���
	$ar_cmd = explode(' ', $command);//��ƥ���������飬����ƥ��
	$order = array("\r\n", "\n", "\r");//׼���滻�ַ�����
	$offset = strpos($content, $keyword);//ƫ����
	$sub_cnt = str_replace($order, ' ', $content);
	while($offset)
	{
		$sub_cnt = substr($sub_cnt, $offset);
		$ar_sub_cnt = explode(' ', $sub_cnt);//��ɾ��ƥ�䲿�ֵ������ļ���������ʽ
		
		for($n = 0; $n < count($ar_cmd); $n++)
		{
			if($ar_cmd[$n] == '?' OR $ar_cmd[$n] == $ar_sub_cnt[$n])
				continue;
			else //�ڶ��?�м��ƥ�����������
				break;
		}
		if($n == count($ar_cmd))//����ȫƥ�䣬��ǰ����ǰ$nλΪ��Ҫ������
		{
			$new_el = $ar_sub_cnt[0];
			for($i = 1; $i < $n; $i++)
			{
				$new_el = $new_el.' '.$ar_sub_cnt[$i];//���ݲ��ҵ�����ƴ���ַ�
			}
			$ar_ret[] = $new_el;
		}
		$offset = strpos($sub_cnt, $keyword, strlen($keyword));//�鿴ʣ���������Ƿ���keyword
	}
	if(count($ar_ret) == 0)
		return false;
	return $ar_ret;
}

$Board_IP =$_GET['Board_IP'];
$ping_result = shell_exec("ping -c 1 $Board_IP");
if(strstr($ping_result,"1 received,"))
{
$dslite_flag = 0;
$result = ssh2execu($Board_IP, 'ps -e| grep dslited');
if(strcmp(substr($result, -8,7),"dslited")==0)
	$dslite_flag = 1;
if($dslite_flag == 0)
{?>
		<script>window.alert('DS-LiteЭ�������δ���������ȿ�������!');</script>
	 	<script>location.href="../transition/transition_overall.php";</script>
<?php
}
if($dslite_flag == 1)
{
$dsliteacl6 = "��δ����";
$dsliteipv6 = "��δ����";
$dsliteip = "��δ����";
$dslitepool = "��δ����";
snmp_set_quick_print(1);
$conf=snmpget($Board_IP, "public",".1.3.6.1.2.1.315.1.1.0");

$piecesconf=explode(" ",$conf);
for($i = 0;$i < count($piecesconf);$i ++)
{
        if(strcmp($piecesconf[$i],"ip")==0 && strcmp($piecesconf[$i+1],"address")== 0) 
        {
                $dsliteip = $piecesconf[$i+2];
		$n=strpos($dsliteip ,'!');
                if ($n) 
                	$dsliteip = substr($dsliteip,0,$n);
        }
        if(strcmp($piecesconf[$i],"ipv6")==0 && strcmp($piecesconf[$i+1],"address")== 0) 
        {
                $dsliteipv6 = $piecesconf[$i+2];
		$n=strpos($dsliteipv6,'!');
                if ($n) 
                	$dsliteipv6 = substr($dsliteipv6,0,$n);
        }
        if(strcmp($piecesconf[$i],"pool")==0) 
        {
                $dslitepool = $piecesconf[$i+1];
		$n=strpos($dslitepool ,'!');
                if ($n) 
                	$dslitepool = substr($dslitepool ,0,$n);
        }
        if(strcmp($piecesconf[$i],"acl6")==0) 
        {
                $dsliteacl6 = $piecesconf[$i+1];
                $n=strpos($dsliteacl6 ,'!');
                if ($n) 
                	$dsliteacl6 = substr($dsliteacl6 ,0,$n);
       }

}
$gateway = cmd_get_val($conf, 'dslite enable');
if($gateway)
        $showstatus = "ʹ ��";
else
        $showstatus = "ʧ ��";

?>
<h4>�豸<?php echo $Board_IP; ?>��DS-LiteЭ��������Ϣ:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >IPv6ǰ׺</td>
		<td class="col-cell  col-cell1"><width="16" height="16"> &nbsp;<?php echo $dsliteacl6;?>&nbsp;  </td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6��ַ</td>
		<td class="col-cell col-cell1"><width="16" height="16">&nbsp;<?php echo $dsliteipv6;?>&nbsp;  </td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv4��ַ</td>
		<td class="col-cell col-cell1"><width="16" height="16">&nbsp;<?php echo $dsliteip;?>&nbsp;  </td>
	</tr>
	<tr>
		<td class="col-cell" >��ַ��</td>
		<td class="col-cell col-cell1"><width="16" height="16">&nbsp;<?php echo $dslitepool;?>&nbsp;  </td>
	</tr>
	<tr>
		<td class="col-cell" >ʹ��״̬</td>
		<td class="col-cell col-cell1"><width="16" height="16">&nbsp;<?php echo $showstatus;?>&nbsp;  </td>
	</tr>		
</table>
</div>

<h4>DS-LiteЭ������:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >��������</td>
		<td class="col-cell col-cell1">
		<form  name="form" method="post"  action="dslite_setting.php?Board_IP=<?php echo $Board_IP; ?>" onSubmit="return checksignup()">
      		IPv6ǰ׺:<input type="text" name="IPv6Prefix" id="IPv6Prefix" class="txt" value="2001:5::/64" style="color:#3C3C3C;"  onfocus="if(value=='2001:5::/64'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001:5::/64'}" /><br>
       		IPv6��ַ:<input type="text" name="AFTRipv6" id="AFTRipv6" class="txt" value="2001:240:63f:ff01::1/128" style="color:#3C3C3C;"  onfocus="if(value=='2001:240:63f:ff01::1/128'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='2001:240:63f:ff01::1/128'}" /><br>
       		IPv4��ַ:<input type="text" name="AFTRipv4" id="AFTRipv4" class="txt" value="192.168.255.111/24" style="color:#3C3C3C;"  onfocus="if(value=='192.168.255.111/24'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.255.111/24'}" /><br>
       		��ַ��:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dslitepool" id="dslitepool" class="txt" value="192.168.255.111/32" style="color:#3C3C3C;"  onfocus="if(value=='192.168.255.111/32'){this.style.color='#3C3C3C';value=''}" onblur="if(value==''){this.style.color='#3C3C3C';value='192.168.255.111/32'}" /><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="submit" value="����" >
    		</form>
		</td>
	</tr>	
	<tr>
		<td class="col-cell" >ʹ��/ʧ��DS-LiteЭ��</td>
		<td class="col-cell col-cell1">
              	<form name="form" method="post" action="enable_dslite.php?Board_IP=<?php echo $Board_IP; ?>" >&nbsp;
	        <select name="dsliteglobalstatus">
		<option value="1">ʹ��</option>
	        <option value="2">ʧ��</option>
	        </select>&nbsp;
		<input  type="submit" value="ȷ��" ></form>
                </td>
	</tr>
	<tr>
		<td class="col-cell" >���̿���</td>
		<td class="col-cell col-cell1">
		<a href="../transition/transition_overall.php"><font color=black>ǰ������Э����̹���</a>
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
		<script>window.alert('�޷�����IP�����ȼ��忨����ͨ��!');</script>
	 	<script>location.href="../transition/transition_overall.php";</script>
<?php
}
?>

</body>
</html>


