<?php include("../../include/cache_start.php"); ?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>��������װ����Ϣ</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 720px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 850px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
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
<script type="text/javascript">
var shmc_ipmb_lst = new Array();
var board_ipmb_lst = new Array();
var shelf_ipmb_lst = new Array();
</script>
</head>
<body>
<?php
include('../atca.class.php');

$atca = new Atca("NULL");
?>

<a href="<?php echo $_SERVER['SCRIPT_NAME']."?cache=0";?>">ˢ��</a>
<form name="form" method="post" action="fru_setting.php" >
<h4>����ģ���б�:</h4>
<div id="main1">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1">���λ��</td>
		<td class="col-cell col-cell1">FRU��ַ</td>
		<td class="col-cell col-cell1">����״̬</td>
		<td class="col-cell col-cell1" >FRUģ������</td>
		<td class="col-cell col-cell1">�״̬</td>
		<td class="col-cell col-cell1">���й���(��λ:��)</td>
		<td class="col-cell col-cell1">��&nbsp;&nbsp;��</td>
	</tr>
	<?php
	$shmc = $atca->loadShmc("NULL");
	for($n = 0, $l = count($shmc); $n < $l; $n++) {
		print('<tr>');
		//���λ��
		print('<td class="col-cell">'.($n+1).'</td>');
		//ipmb��ַ
		print('<td class="col-cell">'.$shmc[$n]->Ipmb_addr.'</td>');
		//����״̬
		($shmc[$n]->Present == 1 ? print('<td class="col-cell">�Ѿ�����</td>') : print('<td class="col-cell">������</td>'));
		//FRUģ������
		($shmc[$n]->Present == 1 ? print('<td class="col-cell" id="'.$shmc[$n]->Ipmb_addr.'_shmc_name">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		//�״̬
		($shmc[$n]->Present == 1 ? print('<td class="col-cell" id="'.$shmc[$n]->Ipmb_addr.'_shmc_state">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		//����״̬
		($shmc[$n]->Present == 1 ? print('<td class="col-cell" id="'.$shmc[$n]->Ipmb_addr.'_shmc_power">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		//�����ر�
		($shmc[$n]->Present == 1 ? print('<td class="col-cell" id="'.$shmc[$n]->Ipmb_addr.'_shmc_switch">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		print('</tr>');
		if($shmc[$n]->Present == 1)
			print('<script type="text/javascript">shmc_ipmb_lst.push("'.$shmc[$n]->Ipmb_addr.'");</script>');
	}
	?>
</table>
</div>

<h4>�����б�:</h4>
<div id="main1">
<table class="features-table">
	<tr>
		<td class="col-cell col-cell1">���λ��</td>
		<td class="col-cell col-cell1">FRU��ַ</td>
		<td class="col-cell col-cell1">����״̬</td>
		<td class="col-cell col-cell1" >FRUģ������</td>
		<td class="col-cell col-cell1">�״̬</td>
		<td class="col-cell col-cell1">���й���(��λ:��)</td>
		<td class="col-cell col-cell1">��&nbsp;&nbsp;��</td>
	</tr>
	<?php
	$board = $atca->loadBoard("NULL");
	for($n = 0, $l = count($board); $n < $l; $n++) {
		print('<tr>');
		//���λ��
		print('<td class="col-cell">'.($n+1).'</td>');
		//ipmb��ַ
		print('<td class="col-cell">'.$board[$n]->Ipmb_addr.'</td>');
		//����״̬
		($board[$n]->Present == 1 ? print('<td class="col-cell">�Ѿ�����</td>') : print('<td class="col-cell">������</td>'));
		//FRUģ������
		($board[$n]->Present == 1 ? print('<td class="col-cell" id="'.$board[$n]->Ipmb_addr.'_board_name">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		//�״̬
		($board[$n]->Present == 1 ? print('<td class="col-cell" id="'.$board[$n]->Ipmb_addr.'_board_state">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		//����״̬
		($board[$n]->Present == 1 ? print('<td class="col-cell" id="'.$board[$n]->Ipmb_addr.'_board_power">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		//�����ر�
		($board[$n]->Present == 1 ? print('<td class="col-cell" id="'.$board[$n]->Ipmb_addr.'_board_switch">��ѯ��...</td>') : print('<td class="col-cell">������</td>'));
		print('</tr>');
		if($board[$n]->Present == 1)
			print('<script type="text/javascript">board_ipmb_lst.push("'.$board[$n]->Ipmb_addr.'");</script>');
	}
	?>
</table>
</div>

<h4>����FRU�б�:</h4><span id="search">��ѯ��...</span>
<div id="main1">
<table class="features-table" id="shelf">
	<tr>
		<td class="col-cell col-cell1" >FRUģ������</td>			
		<td class="col-cell col-cell1">FRU��ַ</td>
		<td class="col-cell col-cell1">FRU ID</td>	
		<td class="col-cell col-cell1">����</td>	
		<td class="col-cell col-cell1">�״̬</td>
		<td class="col-cell col-cell1">���й���(��λ:��)</td>	
	</tr>
	<?php
	$shelf = $atca->getShelfIpmbLst();
	for($n = 0, $l = count($shelf); $n < $l; $n++) {
		print('<script type="text/javascript">shelf_ipmb_lst.push("'.$shelf[$n].'");</script>');
	}
	?>
</table>
</div>
</form>
<script type="text/javascript" src="../../scripts/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../scripts/dict.js" charset="gb2312"></script>
<script type="text/javascript">
function getFrudata(type, ipmb){
	$.ajax({
		url:'getFrudata.php?'+Math.random(),
		type:'get',
		contentType: "application/json",
		dataType:'json',
		data:{"type":type,"ipmb":ipmb},
		success:function(json){
			//��Ҫ����ϲ�����html
			if(type == "shmc" || type == "board") {
				if(parseInt(json[0].State)===1) {//�ѹر�
					$("#"+ipmb+"_"+type+"_switch").html('<a href="fru_setting.php?control=1&ipmb='+ipmb+'&fru_id=0">���� </a>');
					$("#"+ipmb+"_"+type+"_name").html('������');
					$("#"+ipmb+"_"+type+"_state").html('������');
					$("#"+ipmb+"_"+type+"_power").html('������');
				}
				else {
					if(parseInt(json[0].State)===4)//�ѿ���
						$("#"+ipmb+"_"+type+"_switch").html('<a href="fru_setting.php?control=0&ipmb='+ipmb+'&fru_id=0">�ر� </a>|<a href="fruRestart.php?ipmb='+ipmb+'&type='+type+'">����</a>');
					else//����ת��״̬
						$("#"+ipmb+"_"+type+"_switch").html('��ȴ�');
					$("#"+ipmb+"_"+type+"_name").html(json[0].Str_name);
					$("#"+ipmb+"_"+type+"_state").html(stateDict(parseInt(json[0].State)));
					$("#"+ipmb+"_"+type+"_power").html(json[0].Power);
				}
			}
			else {
				for(var i = 0; i < json.length; i++) {
					var line = '<td class="col-cell">';
					var table = $('#shelf');
					var row = $("<tr></tr>");
					row.append("<td>"+json[i].Str_name+"</td>");
					row.append("<td>"+ipmb+"</td>");
					row.append("<td>"+parseInt(json[i].Fru_id)+"</td>");
					row.append("<td>"+frutypeDict(parseInt(json[i].Type))+"</td>");
					row.append("<td>"+stateDict(parseInt(json[i].State))+"</td>");
					row.append("<td>"+json[i].Power);
					table.append(row); 
				}
				$("#search").remove();
			}
		},
		error:function(data){
		}
	});
}
$(document).ready(function(){
	for(i = 0, l = shmc_ipmb_lst.length; i < l; i++){
		getFrudata("shmc", shmc_ipmb_lst[i]);
	}
	for(i = 0, l = board_ipmb_lst.length; i < l; i++){
		getFrudata("board", board_ipmb_lst[i]);
	}
	for(i = 0, l = shelf_ipmb_lst.length; i < l; i++){
		getFrudata("shelf", shelf_ipmb_lst[i]);
	}
});
</script>
</body>
</html>
<?php include("../../include/cache_end.php"); ?>

