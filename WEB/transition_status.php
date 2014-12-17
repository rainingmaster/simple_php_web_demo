<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>过渡协议各板卡基本状态信息</title>
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
<h4>交换板1:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >当前运行的过渡协议：</td>
		<td class="col-cell  col-cell1"><width="16" height="16"> </td>		
	</tr>
	<tr>
		<td class="col-cell" >IP地址: </td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6地址前缀:</td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>
	</tr>
	<tr>
		<td class="col-cell" >更改当前运行的过渡协议:</td>
		<td class="col-cell col-cell1"><width="16" height="16">
              <form name="form" method="post" action="transition_change.php" >&nbsp;
	       <select name="transitionstatus">
	       <option value="1">NAT-PT</option>
	       <option value="2" >有状态NAT64</option>
	       <option value="3">无状态NAT64</option>
	       <option value="4">IVI</option>
	       <option value="5">DS-Lite</option>
	       <option value="6">ECDYSIS</option>
	       </select>&nbsp;
		<input  type="submit" value="确认" ></form>
              </td>
	</tr>	
</table>
</div>

<h4>交换板2:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >当前运行的过渡协议：</td>
		<td class="col-cell  col-cell1"><width="16" height="16"> </td>		
	</tr>
	<tr>
		<td class="col-cell" >IP地址: </td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6地址前缀:</td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>
	</tr>
	<tr>
		<td class="col-cell" >更改当前运行的过渡协议:</td>
		<td class="col-cell col-cell1"><width="16" height="16">
              <form name="form" method="post" action="transition_change.php" >&nbsp;
	       <select name="transitionstatus">
	       <option value="1">NAT-PT</option>
	       <option value="2" >有状态NAT64</option>
	       <option value="3">无状态NAT64</option>
	       <option value="4">IVI</option>
	       <option value="5">DS-Lite</option>
	       <option value="6">ECDYSIS</option>
	       </select>&nbsp;
		<input  type="submit" value="确认" ></form>
              </td>
	</tr>	
</table>
</div>

<h4>交换板3:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >当前运行的过渡协议：</td>
		<td class="col-cell  col-cell1"><width="16" height="16"> </td>		
	</tr>
	<tr>
		<td class="col-cell" >IP地址: </td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6地址前缀:</td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>
	</tr>
	<tr>
		<td class="col-cell" >更改当前运行的过渡协议:</td>
		<td class="col-cell col-cell1"><width="16" height="16">
              <form name="form" method="post" action="transition_change.php" >&nbsp;
	       <select name="transitionstatus">
	       <option value="1">NAT-PT</option>
	       <option value="2" >有状态NAT64</option>
	       <option value="3">无状态NAT64</option>
	       <option value="4">IVI</option>
	       <option value="5">DS-Lite</option>
	       <option value="6">ECDYSIS</option>
	       </select>&nbsp;
		<input  type="submit" value="确认" ></form>
              </td>
	</tr>	
</table>
</div>

<h4>交换板4:</h4>
<div id="main">
<table class="features-table">
	<tr>
		<td class="col-cell" >当前运行的过渡协议：</td>
		<td class="col-cell  col-cell1"><width="16" height="16"> </td>		
	</tr>
	<tr>
		<td class="col-cell" >IP地址: </td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>		
	</tr>
	<tr>
		<td class="col-cell" >IPv6地址前缀:</td>
		<td class="col-cell col-cell1"><width="16" height="16"></td>
	</tr>
	<tr>
		<td class="col-cell" >更改当前运行的过渡协议:</td>
		<td class="col-cell col-cell1"><width="16" height="16">
              <form name="form" method="post" action="transition_change.php" >&nbsp;
	       <select name="transitionstatus">
	       <option value="1">NAT-PT</option>
	       <option value="2" >有状态NAT64</option>
	       <option value="3">无状态NAT64</option>
	       <option value="4">IVI</option>
	       <option value="5">DS-Lite</option>
	       <option value="6">ECDYSIS</option>
	       </select>&nbsp;
		<input  type="submit" value="确认" ></form>
              </td>
	</tr>	
</table>
</div>


</body>
</html>


