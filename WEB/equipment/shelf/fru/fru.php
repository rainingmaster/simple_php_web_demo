<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>FRU信息</title>
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
include('../../atca.class.php');
$atca = new Atca("NULL");
$device_lst = $atca->getPresentLst("FRU");
?>

<h4>FRU设备列表:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1" >FRU设备名</td>
		<td class="col-cell col-cell1" >IPMB地址</td>
		<td class="col-cell col-cell1" >FRU设备ID</td>	
		<td class="col-cell col-cell1" >运行状态</td>
	</tr>
	<?php
	for($n = 0, $l = count($device_lst); $n < $l; $n++)
	{
		for($i = 0, $k = count($device_lst[$n]->Fru_lst); $i < $k; $i++)
		{
			print('<tr>');
			print('<td class="col-cell">'.substr($device_lst[$n]->Fru_lst[$i]->Str_name,1,-1).'</td>');
			print('<td class="col-cell">'.$device_lst[$n]->Fru_lst[$i]->Ipmb_addr.'</td>');
			print('<td class="col-cell">'.$device_lst[$n]->Fru_lst[$i]->Fru_id.'</td>');
			print('<td class="col-cell">'.stateDict($device_lst[$n]->Fru_lst[$i]->State).'</td>');
			print('</tr>');
		}
	}
	?>

</table>
</div>

<h4>FRU设备开启/关闭:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >选择要设置的FRU模块</td>
		<td class="col-cell col-cell1"><width="16" height="16">
             	<form name="form" method="post" action="fru_setting.php" >
		<select name="fru_ipmb_and_id">
		<?php
			for($n = 0, $l = count($device_lst); $n < $l; $n++)
			{
				for($i = 0, $k = count($device_lst[$n]->Fru_lst); $i < $k; $i++)
				{
		?>
			<option value=<?php echo $device_lst[$n]->Fru_lst[$i]->Ipmb_addr."+".$device_lst[$n]->Fru_lst[$i]->Fru_id; ?> >IPMB:<?php echo $device_lst[$n]->Fru_lst[$i]->Ipmb_addr; ?>  ID:<?php echo $device_lst[$n]->Fru_lst[$i]->Fru_id; ?></option>
		<?php
				}
			}	
		?>
	        </select>
		<select name="control">
	        <option value="1" >开启</option>
	        <option value="2">关闭</option>
	        </select>
		<input  type="submit" value="设置" >
		</form>
                </td>
	</tr>	
</table>
</div>

</body>
</html>


