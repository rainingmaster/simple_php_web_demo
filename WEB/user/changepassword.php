<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>修改用户密码</title>
<style type="text/css">    
body{margin: 0;padding: 0;background: white url(/jscss/demoimg/bgdemo2.jpg);}
#main{width: 600px;margin: 10px auto auto 10px;background: white;-moz-border-radius: 8px;-webkit-border-radius: 8px;padding: 30px;border: 1px solid #adaa9f;-moz-box-shadow: 0 2px 2px #9c9c9c;-webkit-box-shadow: 0 2px 2px #9c9c9c;}
.features-table{width: 100%;margin: 0 auto;border-collapse: separate;border-spacing: 0;text-shadow: 0 1px 0 #fff;color: #2a2a2a;background: #fafafa;background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff);/* Firefox 3.6*/background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));font-family: Verdana,Arial,Helvetica}
.features-table td{height: 50px;line-height: 50px;padding: 0 20px;border-bottom: 1px solid #cdcdcd;box-shadow: 0 1px 0 white;-moz-box-shadow: 0 1px 0 white;-webkit-box-shadow: 0 1px 0 white;white-space: nowrap;}
.no-border td{border-bottom: none;box-shadow: none;-moz-box-shadow: none;-webkit-box-shadow: none;}
.col-cell{text-align: center;width: 150px;font: normal 1em Verdana, Arial, Helvetica;}
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
<div id="main">
<form  name="form" method="post"  action="changepasswordsucessful.php" enctype="multipart/form-data"  onSubmit="return checksignup()">
<div class="gateway">
    <ul>
       <li>用&nbsp;户&nbsp;名:&nbsp;&nbsp;&nbsp;&nbsp;<input type=text name=username id=username></li>
       <li>新&nbsp;密&nbsp;码:&nbsp;&nbsp;&nbsp;&nbsp;<input type=password name=password id=password></li>
    	<li>再输入新密码:<input type=password name=password1 id=password1 class=></li>
	<input  type="submit" value="确认" >
    </ul>
</div>
</form>

</div>
</body>
</html>
<script>
function checksignup()
{
	if ( document.form.username.value == '' )
	{
		window.alert('Please Input User Name!');
		document.form.username.focus();
	}
	else if
	( document.form.password.value == '' ) {
		window.alert('Please Input New Password !');
		document.form.password.focus();
	}
	else if
	( document.form.password1.value == '' ) {
		window.alert('Please Input new Password again !');
		document.form.password1.focus();
	}
   else
   {
	return true;
	}
	return false;
}
</script>







