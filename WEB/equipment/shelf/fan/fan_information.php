<?php header('Cache-control: max-age=10'); ?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>������Ϣ</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 650px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
<?php
	include('../../atca.class.php');
	
	if(isset($_GET['ipmb'])) {
		$ipmb = $_GET['ipmb'];
		if(isset($_GET['name']))
			$name = $_GET['name'];
		else {
			$ipmc = new Ipmc($ipmb);
			$name = $ipmc->Str_name;
		}
	}
	else {
		$atca = new Atca("NULL");
		$shelf = $atca->loadShelf("IPMC");//����shelf������з���
		if(count($shelf) === 0 || $shelf[0]->Ipmc === false) {
			echo "</head><body>";
			echo "����δ���룬���飡";
			echo "</body></html>";
			exit;
		}
		$ipmb = $shelf[0]->Ipmc->Ipmb_addr;
		$name = substr($shelf[0]->Ipmc->Str_name, 1, -1);
	}
	
	$device = new Device($ipmb, 1, "Fan", "NULL");
	$getFanSensorLst = $device->getFanSensorLst();
?>
<script type="text/javascript">
var ipmb = <?php echo $ipmb;?>;
var id_lst = new Array();
</script>
</head>
<body>
<?php
	if( count($getFanSensorLst) == 0)
	{
		?>
		<h4>ģ��<?php echo $name; ?>û�пɲ�ѯ�ķ����豸</h4>
	<?php
	}
	else
	{
	?>
<h4>ģ��<?php echo $name; ?>�ķ����豸��Ϣ:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1" >�����豸����</td>
		<td class="col-cell col-cell1" >IPMB��ַ</td>
		<td class="col-cell col-cell1" >�����豸ID</td>
		<td class="col-cell col-cell1">��ǰת��(��λ:ת/����)</td>
		<td class="col-cell col-cell1">ת����ֵ(��λ:ת/����)</td>	
	</tr>
	<?php
		for($i = 0; $i < count($getFanSensorLst); $i++)
		{
	?>
			<tr>
			<td class="col-cell" > <?php echo $getFanSensorLst[$i]->Str_name; ?> </td>
			<td class="col-cell" ><?php echo $getFanSensorLst[$i]->Owner_ipmb; ?></td>
			<td class="col-cell" ><?php echo $getFanSensorLst[$i]->Id; ?></td>
			<td class="col-cell" ><?php echo round($getFanSensorLst[$i]->Cur_val); ?></td>
			<td class="col-cell" id="<?php echo $getFanSensorLst[$i]->Id; ?>_box">��ѯ��...
				<script type="text/javascript">
					id_lst.push("<?php echo $getFanSensorLst[$i]->Id; ?>");
				</script>
			</td>
			</tr>
	<?php
		}
	?>
</table>
</div>

<h4>ת����ֵ����:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="fan_threshold_setting.php?ipmb=<?php echo $ipmb; ?>" >
	<tr>
		<td class="col-cell" >ѡ��Ҫ���õķ��ȼ�������ֵ</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		��ֵ:<input type="text" name="Upper_critical_threshold" id="Upper_critical_threshold" class="txt" size="6">&nbsp;&nbsp;
		�豸ID:<select name="fan_id">
		<?php
		for($i = 0; $i < count($getFanSensorLst); $i++) 
		{
		?>
               	<option value=<?php echo $getFanSensorLst[$i]->Id; ?> ><?php echo $getFanSensorLst[$i]->Id; ?></option>
		<?php
		}
		?>
	       	</select>&nbsp;&nbsp;
		<input  type="submit" value="����" >
              </td>
	</tr>
</form>
</table>
</div>

<h4>�������̵ȼ�:</h4><span id="search">��ѯ��...</span>
<div id="main1">
<table class="features-table" id="fantray">
	<tr>
		<td class="col-cell col-cell1" >������������</td>
		<td class="col-cell col-cell1" >����ID</td>
		<td class="col-cell col-cell1" >�ȼ���Χ</td>
		<td class="col-cell col-cell1" >��ǰ�ȼ�</td>
	</tr>
</table>
</div>

<h4>���̵ȼ�����:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="fan_tray_setting.php?ipmb=<?php echo $ipmb; ?>" >
	<tr>
		<td class="col-cell" >ѡ��Ҫ���õ�����ID���ȼ�</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		�ȼ�:<input type="text" name="level" id="level" class="txt" size="6">&nbsp;&nbsp;
		����ID:<select name="fantray_id" id="fantray_id">
	       	</select>&nbsp;&nbsp;
			<input  type="submit" value="����" >
        </td>
	</tr>
</form>
</table>
</div>

<?php
	}
?>


<script type="text/javascript" src="../../../scripts/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
function getThreshold(ipmb, id){
	var cont = {"ipmb":ipmb,"id":id};
	$.ajax({
		url:'../getThreshold.php?'+Math.random(),
		type:'get',
		dataType:'json',
		data:cont,
		success:function(data){
		//��Ҫ����ϲ�����html
		 var str = ">" + Math.round(data.lc);
			$("#"+id+"_box").html(str);
		},
		error:function (XMLHttpRequest, textStatus, errorThrown) {
			$("#"+id+"_box").html("���ӳ�ʱ");
		}
	});
}

function getFantray(ipmb){
	var cont = {"ipmb":ipmb};
	$.ajax({
		url:'get_fan_tray.php?'+Math.random(),
		type:'get',
		dataType:'json',
		data:cont,
		success:function(json){
			for(var i = 0; i < json.length; i++) {
				var line = '<td class="col-cell">';
				var table = $('#fantray');
				var row = $("<tr></tr>");
				row.append("<td class='col-cell'>"+json[i].Str_name+"</td>");
				row.append("<td class='col-cell'>"+json[i].Fru_id+"</td>");
				row.append("<td class='col-cell'>"+json[i].Minfanlevel+"~"+json[i].Maxfanlevel+"</td>");
				row.append("<td class='col-cell'>"+json[i].Curfanlevel+"</td>");
				table.append(row);
				$("#fantray_id").append("<option>"+json[i].Fru_id+"</option>");   //ΪSelect׷��һ��Option(������)
			}
			$("#search").remove();
		},
		error:function (XMLHttpRequest, textStatus, errorThrown) {
		}
	});
}

//only get lc
$(document).ready(function(){
	for(i = 0; i < id_lst.length; i++){
		getThreshold(ipmb, id_lst[i]);
	}
	
	getFantray(ipmb);//��ȡ�������̵ȼ�
});
</script>
</body>
</html>


