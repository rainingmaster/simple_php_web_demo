<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>网口流量</title>
<style type="text/css">   
#main{width: 800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
#main1{width: 800px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 15px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 100%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 30px;line-height: 35px;padding: 0 20px;border-bottom: 1px solid #cdcdcd;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
.no-border td{border-bottom: none;box-shadow: none;-moz-box-shadow: none;-webkit-box-shadow: none;}
.col-cell{text-align: center;width: 100px;font: normal 1em Verdana, Arial, Helvetica;}
.col-cell1, .col-cell2{background: #efefef;background: rgba(144,144,144,0.15);border-right: 1px solid white;}
.col-cell3{background: #e7f3d4;background: rgba(184,243,85,0.3);}
.col-cellh{font: bold 1.3em 'trebuchet MS', 'Lucida Sans', Arial;-moz-border-radius-topright: 10px;-moz-border-radius-topleft: 10px;border-top-right-radius: 10px;border-top-left-radius: 10px;border-top: 1px solid #eaeaea !important;}
.col-cellf{font: bold 1.4em Georgia;-moz-border-radius-bottomright: 10px;-moz-border-radius-bottomleft: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;border-bottom: 1px solid #dadada !important;}
</style>
<script type="text/javascript">
var ip_lst = new Array();
var flow_mess = new Array();
<?php 
include("../include/ini.class.php");
$ini = new ini();
$board_count = $ini->iniRead("device_info", "board_count");
for($i = 1; $i <= $board_count; $i++)
{
	$ip = $ini->iniRead("board".$i, "ipaddr");
	print('ip_lst.push("'.$ip.'");');
	print('flow_mess['.($i - 1).'] = new Array();');
}
 ?>
</script>
</head>
<body>
<input id="fresh" type="button" value="启动刷新" onclick="freshClick()" />
<?php for($i = 0; $i < $board_count; $i++) { ?>
<h4>&nbsp;&nbsp;board<?php echo $i+1; ?>收发包状况:</h4>
<div id="main1">
	<table id="flow<?php echo $i; ?>" class="features-table">
		<thead>
			<tr>
				<td class="col-cell" >接口名</td>
				<td class="col-cell" >接收总字节(bytes)</td>
				<td class="col-cell" >每秒接收字节数</td>
				<td class="col-cell" >发送总字节(bytes)</td>
				<td class="col-cell" >每秒发送字节数</td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<?php }//end "<h4>&nbsp;&nbsp;配置" ?>
<script type="text/javascript" src="../scripts/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
var fresh_flag;//标示是否刷新

function setRecord(num, json) {
	for(var i = 0; i < json.length; i++) {
		if_mess = new Array();
		if_mess['if_name'] = json[i].if_name;
		if_mess['rece_bytes'] = json[i].rece_bytes;
		if_mess['rece_packets'] = json[i].rece_packets
		if_mess['trans_bytes'] = json[i].trans_bytes;
		if_mess['trans_packets'] = json[i].trans_packets;
		flow_mess[num].push(if_mess);
	}
}
function getNetpps(num, ip){
	var cont = {"ip":ip};
	$.ajax({
		url:'flow.php?'+Math.random(),
		type:'get',
		dataType:'json',
		data:cont,
		success:function(json){
			if(flow_mess[num].length == 0) {//第一次载入
				for(var i = 0; i < json.length; i++) {
					var table = $('#flow'+num);
					var row = $("<tr></tr>");
					row.append("<td class='col-cell' id='if_name_"+num+"_"+i+"'>"+json[i].if_name+"</td>");
					row.append("<td class='col-cell' id='rece_bytes_"+num+"_"+i+"'>"+json[i].rece_bytes+"</td>");
					row.append("<td class='col-cell' id='rs_bytes_"+num+"_"+i+"'>"+0+"</td>");
					row.append("<td class='col-cell' id='trans_bytes_"+num+"_"+i+"'>"+json[i].trans_bytes+"</td>");
					row.append("<td class='col-cell' id='ts_bytes_"+num+"_"+i+"'>"+0+"</td>");
					table.append(row);
				}
			}
			else {//之后的载入
				for(var i = 0; i < json.length; i++) {
					$("#if_name_"+num+"_"+i).html(json[i].if_name);
					$("#rece_bytes_"+num+"_"+i).html(json[i].rece_bytes);
					$("#rs_bytes_"+num+"_"+i).html((parseInt(json[i].rece_bytes)-parseInt(flow_mess[num][i]['rece_bytes'])));
					$("#trans_bytes_"+num+"_"+i).html(json[i].trans_bytes);
					$("#ts_bytes_"+num+"_"+i).html((parseInt(json[i].trans_bytes)-parseInt(flow_mess[num][i]['trans_bytes'])));
				}
			}
			flow_mess[num].length = 0;
			setRecord(num, json);
		},
		error:function (XMLHttpRequest, textStatus, errorThrown) {
		}
	});
}

function getAllNetpps() {
	for(i = 0; i < ip_lst.length; i++){
		getNetpps(i, ip_lst[i]);
	}
}

function firstLoad() {
	getAllNetpps();
	setTimeout(getAllNetpps, 500);
}

function freshClick() {//startFresh
	if ($("#fresh").val()=="启动刷新") {
		fresh_flag = setInterval(getAllNetpps,1000);
		$("#fresh").val("停止刷新");
	}
	else {//stopFresh
		clearInterval(fresh_flag);
		$("#fresh").val("启动刷新");
	}
}

$(document).ready(function(){
	firstLoad();
});
</script>
</body>
</html>