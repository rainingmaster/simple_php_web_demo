<?php header('Cache-control: max-age=10'); ?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>电压信息</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 1050px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
		$liveDevice = $atca->getLiveLst("IPMC");
		$ipmb = $liveDevice[0]->Ipmc->Ipmb_addr;
		$name = substr($liveDevice[0]->Ipmc->Str_name, 1, -1);
	}
	
	$device = new Device($ipmb, 1, "Temper", "NULL");
	$getTemperSensorLst = $device->getTemperSensorLst();
?>
<script type="text/javascript">
var ipmb = <?php echo $ipmb;?>;
var id_lst = new Array();
</script>
</head>

<body>
<?php
	if( count($getTemperSensorLst) == 0)
	{
		?>
		<h4>模块<?php echo $name; ?>没有可查询的温度设备</h4>
	<?php
	}
	else
	{
	?>
<h4>模块<?php echo $name; ?>的温度设备信息:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1" >温度设备名称</td>
		<td class="col-cell col-cell1" >IPMB地址</td>
		<td class="col-cell col-cell1" >温度设备ID</td>
		<td class="col-cell col-cell1">当前温度(单位:摄氏度)</td>
		<td class="col-cell col-cell1">警告温度临界值(单位:摄氏度)</td>	
	</tr>
	<?php
		for($i = 0; $i < count($getTemperSensorLst); $i++) 
		{
	?>
			<tr>
			<td class="col-cell" > <?php echo $getTemperSensorLst[$i]->Str_name; ?> </td>
			<td class="col-cell" ><?php echo $getTemperSensorLst[$i]->Owner_ipmb; ?></td>
			<td class="col-cell" ><?php echo $getTemperSensorLst[$i]->Id; ?></td>
			<td class="col-cell" ><?php echo $getTemperSensorLst[$i]->Cur_val; ?></td>
			<td class="col-cell" id="<?php echo $getTemperSensorLst[$i]->Id; ?>_box">查询中...
				<script type="text/javascript">
					id_lst.push("<?php echo $getTemperSensorLst[$i]->Id; ?>");
				</script>
			</td>
			</tr>
	<?php
		}
	?>


</table>
</div>

<h4>温度阈值设置:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="temperature_threshold_setting.php?ipmb=<?php echo $ipmb; ?>" >	
	<tr>
		<td class="col-cell" >警告温度上下限</td>
		<td class="col-cell col-cell1"><width="16" height="16">
	 	下界:<input type="text" name="Lower_critical_threshold" id="Lower_critical_threshold" class="txt" size="6">
		上界:<input type="text" name="Upper_non_critical_threshold" id="Upper_non_critical_threshold" class="txt" size="6">
              </td>
	</tr>
	<tr>
		<td class="col-cell" >设备ID</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		<select name="temperature_id">
		<?php
		for($i = 0; $i < count($getTemperSensorLst); $i++) 
		{
		?>
               	<option value=<?php echo $getTemperSensorLst[$i]->Id; ?> ><?php echo $getTemperSensorLst[$i]->Id; ?></option>
		<?php
		}
		?>
	       	</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="submit" value="设置" >
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
		//按要求组合并填入html
			var str = data.lc+"~"+data.unc;
			$("#"+id+"_box").html(str);
		},
		error:function (XMLHttpRequest, textStatus, errorThrown) {
			$("#"+id+"_box").html("连接超时");
		}
	});
}
//only get lc
$(document).ready(function(){
	for(i = 0; i < id_lst.length; i++){
		getThreshold(ipmb, id_lst[i]);
	}
});
</script>
</body>
</html>


