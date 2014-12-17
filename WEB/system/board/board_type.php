<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�������͹���</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white;}
#main{width: 800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 700px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 100%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 30px;line-height: 35px;padding: 0 20px;border-bottom: 1px solid #aaaaaa;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
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
include('../../include/ssh2.php');
$ini = new ini();
$board_count = $ini->iniRead("device_info", "board_count");
$shmc_count = $ini->iniRead("device_info", "shmc_count");
?>

<h4>&nbsp;&nbsp;�忨����������Ϣ:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1">�忨����</td>
		<td class="col-cell col-cell1">�忨IP</td>
		<td class="col-cell col-cell1">����</td>
		<td class="col-cell col-cell1">·��Э��</td>
		<td class="col-cell col-cell1">����Э��</td>
	</tr>
	<?php
		for($i = 1; $i <= $board_count; $i++) 
		{
	?>
		<tr>
		<td class="col-cell col-cell1" >board<?php echo $i; ?></td>
		<td class="col-cell col-cell1" ><?php echo $ini->iniRead("board".$i, "ipaddr"); ?></td>
		<td class="col-cell col-cell1" >
		<?php 
		switch($ini->iniRead("board".$i, "type")) 
		{
			case "compute":
				echo "�����";
				break;
			case "switch":
				echo "������";
				break;
			default:
				echo "����������";
		}
		?>
		</td>
		<td class="col-cell col-cell1" >
		<?php 
		if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
			echo "��������";
		else
			echo "��������";

		?>
		</td>
		<td class="col-cell col-cell1" >
		<?php 
		if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
			echo "��������";
		else
			echo "��������";

		?>
		</td>
		</tr>
		<?php 
		}
		?>
</table>
</div>

<h4>&nbsp;&nbsp;�忨��������:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="board_type_setting.php" >
	<tr>
		<td class="col-cell" >ѡ��Ҫ���õİ忨������</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		�忨��<select name="board_id">
		<?php
		for($i = 1; $i <= $board_count; $i++) 
		{
		?>
		<option value=<?php echo $i; ?> >board<?php echo $i; ?></option>
		<?php
		}
		?>
		</select>&nbsp;
		���ͣ�<select name="type">
  		<option value=1>�����</option>
  		<option value=2>������</option>
		</select>
		<input  type="submit" value="����" >
              </td>
	</tr>
</form>
</table>
</div>

<h4>&nbsp;&nbsp;�忨��������:</h4>
<div id="main1">
<table class="features-table">
<form name="form" method="post" action="board_function_setting.php" >
	<tr>
		<td class="col-cell" >ѡ��Ҫ���õİ忨������</td>
		<td class="col-cell col-cell1"><width="16" height="16">
		�忨��<select name="board_id">
		<?php
		for($i = 1; $i <= $board_count; $i++) 
		{
		?>
		<option value=<?php echo $i; ?> >board<?php echo $i; ?></option>
		<?php
		}
		?>
		</select>&nbsp;
		���ܣ�<select name="function">
  		<option value=1>������·��Э�顢����Э��</option>
  		<option value=2>��������·��Э��</option>
  		<option value=3>�������ù���Э��</option>
  		<option value=4>��������·��Э�顢����Э��</option>
		</select>
		<input  type="submit" value="����" >
              </td>
	</tr>
</form>
</table>
</div>

</body>
</html>



